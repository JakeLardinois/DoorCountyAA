Note that to utilize a copy of the database from Prod, you only have to use the following SQL

	SELECT * FROM dcaa.wp_options WHERE option_name IN ('siteurl','home');

And update those values from "http://doorcountyaa.org/" to "http://localhost:19320"