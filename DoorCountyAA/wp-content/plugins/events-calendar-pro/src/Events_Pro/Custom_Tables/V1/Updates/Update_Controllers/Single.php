<?php
/**
 * Hooks on the WordPress IDENTIFY, WRITE and READ phases to break an Occurrence
 * out of the original Recurring Event and update the original and new Events.
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers;

use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Admin\Notices\Provider as Notices_Provider;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Events;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Redirector;
use TEC\Events_Pro\Custom_Tables\V1\Updates\Requests;
use Tribe__Events__Pro__Editor__Recurrence__Blocks_Meta as Blocks_Meta;
use WP_Post;
use Tribe__Timezones as Timezones;

/**
 * Class Single
 *
 * @since   6.0.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Updates\Update_Controllers
 */
class Single implements Update_Controller_Interface {
	use Update_Controller_Methods;

	/**
	 * A reference to the current Events repository handler.
	 *
	 * @since 6.0.0
	 *
	 * @var Events
	 */
	private $events;

	/**
	 * The ID of the Event post created by the Update Controller.
	 *
	 * @since 6.0.0
	 *
	 * @var int|null
	 */
	private $single_post_id;

	/**
	 * A reference to the current Requests handler.
	 *
	 * @since 6.0.1
	 *
	 * @var Requests
	 */
	private $requests;
	/**
	 * A reference to the current broewser and request redirection handler.
	 *
	 * @since 6.0.1
	 *
	 * @var Redirector
	 */
	private $redirector;

	/**
	 * Single constructor.
	 *
	 * @since 6.0.0
	 *
	 * @param Events   $events   A reference to the current Events repository handler.
	 * @param Requests $requests A reference to the current Requests handler.
	 */
	public function __construct( Events $events, Requests $requests, Redirector $redirector ) {
		$this->events = $events;
		$this->requests = $requests;
		$this->redirector = $redirector;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.0.0
	 */
	public function apply_before_identify_step( $post_id ) {
		if ( false === ( $post = $this->check_step_requirements( $post_id ) ) ) {
			return false;
		}

		$this->save_request_id( $post_id );

		// We are trashing an occurrence?
		$is_delete = $this->request->get_param('action') === 'trash' ;

		// 1. Duplicate the original Event.
		$single_post = $this->events->duplicate(
			$post,
			// Keep the same post status as the original Event.
			[ 'post_status' => get_post_field( 'post_status', $post ) ]
		);

		// Remove notices from watching the other events being updated
		tribe( Notices_Provider::class )->unregister();

		if ( ! $single_post instanceof WP_Post ) {
			do_action( 'tribe_log', 'error', 'Failed to create Event on Single update.', [
				'source'  => __CLASS__,
				'slug'    => 'duplicate-fail-on-single-update',
				'post_id' => $post_id,
			] );

			return false;
		}

		$post_id = $post->ID;

		$this->single_post_id = $single_post->ID;
		$occurrence_id        = $this->occurrence->occurrence_id;
		$occurrence_date      = $this->occurrence->start_date;

		$is_first = Occurrence::is_first( $occurrence_id );
		$is_last  = Occurrence::is_last( $occurrence_id );

		if ( $is_first ) {
			// 3. Decrement count limit now that we are subtracting one event.
			$this->events->decrement_event_count_limit_by( $post_id, 1 );


			// Then Update the original Event to start on the second Occurrence.
			$second = Occurrence::where( 'post_id', $post_id )
			                    ->order_by( 'start_date', 'ASC' )
			                    ->offset( 1 )
			                    ->first();

			if ( $second instanceof Occurrence ) {
				$this->events->move_event_date( $post_id, $second );
			}
		} elseif ( $is_last ) {
			// 3. Update the original Event Recurrence meta to end before the Occurrence date.
			$previous_occurrence = Occurrence::where( 'post_id', '=', $post_id )
			                                 ->order_by( 'start_date', 'DESC' )
			                                 ->where( 'start_date', '<', $this->occurrence->start_date )
			                                 ->first();

			if (
				$previous_occurrence instanceof Occurrence
				&& ! $this->events->set_until_limit_on_event( $post_id, $previous_occurrence->start_date )
			) {
				do_action( 'tribe_log', 'error', 'Failed to set UNTIL limit on original Event.', [
					'source'  => __CLASS__,
					'slug'    => 'set-until-limit-fail-on-single-update',
					'post_id' => $post_id,
				] );
			}
		} else {
			$skip_exclusion = false;
			if ( $this->occurrence->is_rdate && $is_delete ) {
				// If we are deleting an RDATE, we will skip the exclusion (the RRULE occurrence is still valid).
				$skip_exclusion = $this->events->remove_rdate_from_event( $post_id, $this->occurrence->start_date );
			}
			if ( ! $skip_exclusion ) {
				// 3. Update the original Event Recurrence meta to add an exclusion on this event date.
				$this->events->add_date_exclusion_to_event( $post_id, $occurrence_date );
			}
		}

		/*
		 * Assign the Occurrence to the single Event to give it a chance to
		 * recycle it.
		 */
		$this->events->transfer_occurrences_from_to(
			$post_id,
			$this->single_post_id,
			'start_date = %s',
			$this->occurrence->start_date
		);

		// 4. Before the Custom Tables are updated, clear the Recurrence rules for this Event.
		add_action( 'tec_events_custom_tables_v1_update_post_before', [ $this, 'ensure_no_recurrence_meta' ] );

		$this->ensure_request_meta( $this->request );
		$this->save_rest_request_recurrence_meta( $this->single_post_id, $this->request );

		if ( $this->requests->is_link_update_request( $this->request ) ) {
			$this->update_event_from_occurrence( $this->single_post_id, $this->occurrence );
			$this->redirector->redirect_to_edit_link( $this->single_post_id );
		}

		return $this->single_post_id;
	}

	/**
	 * Removes the Recurrence meta that might have been set for the created Single Event.
	 *
	 * @since 6.0.0
	 *
	 * @param int $post_id The post ID of the event for which the pre-commit process
	 *                     is running.
	 */
	public function ensure_no_recurrence_meta( int $post_id ): void {
		if ( $post_id !== $this->single_post_id ) {
			return;
		}

		remove_action( current_action(), [ $this, 'ensure_no_recurrence_meta' ] );

		$this->delete_recurrence_meta( $post_id );
	}

	/**
	 * Updates the Event date-related meta from the Occurrence.
	 *
	 * @since 6.0.1
	 *
	 * @param int        $post_id    The post ID of the Event to update.
	 * @param Occurrence $occurrence A reference to the Occurrence to use as the source of the data.
	 *
	 * @return void The Event date-related meta is updated.
	 */
	private function update_event_from_occurrence( int $post_id, Occurrence $occurrence ): void {
		$meta = [
			'_EventStartDate'    => $occurrence->start_date,
			'_EventEndDate'      => $occurrence->end_date,
			'_EventDuration'     => $occurrence->duration,
			'_EventStartDateUTC' => $occurrence->start_date_utc,
			'_EventEndDateUTC'   => $occurrence->end_date_utc,
		];

		foreach ( $meta as $meta_key => $meta_value ) {
			// The function will return `false` on failure and same value, not helpful to check.
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

		$this->delete_recurrence_meta( $post_id );
	}

	/**
	 * Deletes the Recurrence meta for the given Event.
	 *
	 * @since 6.0.1
	 *
	 * @param int $post_id The post ID of the Event to delete the Recurrence meta for.
	 *
	 * @return void The Recurrence meta is deleted.
	 */
	private function delete_recurrence_meta( int $post_id ): void {
		delete_post_meta( $post_id, '_EventRecurrence' );
		delete_post_meta( $post_id, Blocks_Meta::$rules_key );
		delete_post_meta( $post_id, Blocks_Meta::$exclusions_key );
		delete_post_meta( $post_id, Blocks_Meta::$description_key );
	}
}
