<?php

namespace W3SCloud\WooZoho\Zoho\Auth;

use Exception;
use zcrmsdk\crm\utility\Logger;
use zcrmsdk\oauth\exception\ZohoOAuthException;
use zcrmsdk\oauth\persistence\ZohoOAuthPersistenceInterface;
use zcrmsdk\oauth\utility\ZohoOAuthTokens;

/**
 * Class CRMOAuthPersistence.
 * It handle OAuth for zoho.
 *
 * @package W3SCloud\WooZoho\Zoho\Auth
 */
class CRMOAuthPersistence implements ZohoOAuthPersistenceInterface {

	/**
	 * Save auth tokens to db.
	 *
	 * @param ZohoOAuthTokens $zohoOAuthTokens Zoho Auth Tokens.
	 */
	public function saveOAuthData( $zohoOAuthTokens ) {
		global $wpdb;
		try {
			self::deleteOAuthTokens( $zohoOAuthTokens->getUserEmailId() );

			$inserted = $wpdb->insert(
				$wpdb->prefix . 'w3swoozoho_auths',
				array(
					'useridentifier' => $zohoOAuthTokens->getUserEmailId(),
					'accesstoken'    => $zohoOAuthTokens->getAccessToken(),
					'refreshtoken'   => $zohoOAuthTokens->getRefreshToken(),
					'expirytime'     => $zohoOAuthTokens->getExpiryTime(),
				),
				array(
					'%s',
					'%s',
					'%s',
					'%d',
				)
			);

			if ( ! $inserted ) {
				Logger::severe( 'OAuth token insertion failed.' );
			}
		} catch ( Exception $ex ) {
			Logger::severe( "Exception occurred while inserting OAuthTokens into DB({$ex->getMessage()})\n{$ex}" );
		}
	}

	/**
	 * Get Auth token from db.
	 *
	 * @param string $userEmailId Zoho User email id.
	 *
	 * @return ZohoOAuthTokens
	 *
	 * @throws Exception
	 */
	public function getOAuthTokens( $userEmailId ) {
		global $wpdb;
		$o_auth_tokens = new ZohoOAuthTokens();
		try {
			$result_array = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}w3swoozoho_auths WHERE useridentifier=%s", $userEmailId )
			);
			if ( ! $result_array ) {
				Logger::severe( 'Getting result set failed:' );
				throw new ZohoOAuthException( 'No Tokens exist for the given user-identifier,Please generate and try again.' );
			} else {
				foreach ( $result_array as $result ) {
					$o_auth_tokens->setExpiryTime( $result->expirytime );
					$o_auth_tokens->setRefreshToken( $result->refreshtoken );
					$o_auth_tokens->setAccessToken( $result->accesstoken );
					$o_auth_tokens->setUserEmailId( $result->useridentifier );
					break;
				}
			}
		} catch ( Exception $ex ) {
			Logger::severe( "Exception occurred while getting OAuthTokens from DB({$ex->getMessage()})\n{$ex}" );
		}
		return $o_auth_tokens;
	}

	/**
	 * Delete auth token from db.
	 *
	 * @param string $userEmailId Zoho User email id.
	 */
	public function deleteOAuthTokens( $userEmailId ) {
		global $wpdb;
		try {
			$deleted = $wpdb->delete(
				$wpdb->prefix . 'w3swoozoho_auths',
				array( 'useridentifier' => $userEmailId ),
				array( '%s' )
			);
			if ( ! $deleted ) {
				Logger::severe( 'Deleting  oauth tokens failed' );
			}
		} catch ( Exception $ex ) {
			Logger::severe( "Exception occurred while Deleting OAuthTokens from DB({$ex->getMessage()})\n{$ex}" );
		}
	}
}
