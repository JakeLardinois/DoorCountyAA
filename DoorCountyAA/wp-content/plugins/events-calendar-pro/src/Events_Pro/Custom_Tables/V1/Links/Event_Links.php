<?php
/**
 * Handles the Event's link modifications.
 *
 * @since   6.0.11
 *
 * @package TEC\Events\Custom_Tables\V1\Links
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Links;

use DateTime;
use TEC\Events\Custom_Tables\V1\Models\Builder;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Custom_Tables\V1\Models\Provisional_Post;
use TEC\Events_Pro\Custom_Tables\V1\WP_Query\Custom_Query_Filters;
use Tribe__Cache;
use Tribe__Cache_Listener;
use Tribe__Date_Utils;
use WP_Post;

/**
 * Handles modifying the Event Permalink.
 *
 * @since   6.0.11
 *
 * @package TEC\Events\Custom_Tables\V1\Links
 */
class Event_Links {

	/**
	 * Filters the event sequence number sometimes used for permalinks on recurring event URLs.
	 *
	 * @since 6.0.11
	 *
	 * @param mixed   $sequence_number The sequence number we are filtering.
	 * @param WP_Post $post            The post object for this event.
	 *
	 * @return mixed The resolved occurrence ID or original sequence number.
	 */
	public function filter_recurring_event_sequence_number( $sequence_number, WP_Post $post ) {
		$provisional_post = tribe( Provisional_Post::class );
		if ( ! $provisional_post->is_provisional_post_id( $post->ID ) ) {
			return $sequence_number;
		}

		// Prep for cache, will avoid expensive validation.
		$cache_key                = __METHOD__ . '_' . $post->ID;
		$cache                    = tribe_cache();
		$resolved_sequence_number = $cache->get( $cache_key, Tribe__Cache_Listener::TRIGGER_SAVE_POST, false );
		if ( is_numeric( $resolved_sequence_number ) && ! empty( $resolved_sequence_number ) ) {
			return $resolved_sequence_number;
		}

		// Default to sequence input moving forward, we will overwrite if we find a relevant ID.
		$resolved_sequence_number = $sequence_number;

		// Confirm this is a legitimate occurrence.
		$occurrence = Occurrence::find( $provisional_post->normalize_provisional_post_id( $post->ID ) );
		if ( ! $occurrence instanceof Occurrence ) {
			do_action( 'tribe_log',
				'error',
				__CLASS__,
				[ 'message' => "Failed to locate occurrence for $post->ID in eventSequence permalink generation." ]
			);

			// Something went wrong, bail.
			return $resolved_sequence_number;
		}


		/**
		 * Search for an occurrence on the same day. These eventSequence routes are only for
		 * occurrences that cannot route because multiple exist with the same start date.
		 */
		$start                  = Tribe__Date_Utils::build_date_object( $occurrence->start_date );
		$occurrence_on_same_day = Custom_Query_Filters::occurrence_where_same_day( $start )
			->where( 'occurrence_id', '!=', $occurrence->occurrence_id )
			->where( 'post_id', '=', $occurrence->post_id )
			->first();

		// Is this a candidate for eventSequence?
		if ( $occurrence_on_same_day instanceof Occurrence ) {
			// We want to use the day's occurrence (aka Event Sequence) (1 through n) in the route/permalink.
			$occurrences = Custom_Query_Filters::occurrence_where_same_day( $start )
				->where( 'post_id', '=', $occurrence->post_id )
				->order_by( 'start_date', 'ASC' )
				->get();
			$sequence    = 0;
			// Find which occurrence in this day this is.
			foreach ( $occurrences as $check_occurrence_sequence ) {
				$sequence ++;
				if ( $check_occurrence_sequence->occurrence_id === $occurrence->occurrence_id ) {
					$resolved_sequence_number = $sequence;
					break;
				}
			}
			if ( $sequence === 0 ) {
				// Something went wrong, tell about it.
				do_action( 'tribe_log',
					'error',
					__CLASS__,
					[ 'message' => "Failed to locate this occurrence in the set of occurrences for this day. Post $post->ID in eventSequence permalink generation." ]
				);
			}
		}

		// Cache either way, so we don't perform this check over and over for the same ID.
		$cache->set( $cache_key, $resolved_sequence_number, Tribe__Cache::NO_EXPIRATION, Tribe__Cache_Listener::TRIGGER_SAVE_POST );

		return $resolved_sequence_number;
	}
}
