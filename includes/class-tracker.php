<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTP_Tracker {
		/** Init hooks */
		public static function init() {
				add_action( 'wp_ajax_rtp_track_view', array( __CLASS__, 'track' ) );
				add_action( 'wp_ajax_nopriv_rtp_track_view', array( __CLASS__, 'track' ) );
				add_action( 'wp_enqueue_scripts', array( __CLASS__, 'localize' ), 20 );
		}

		/** Localize script */
		public static function localize() {
				wp_localize_script( 'rtp-front', 'RTPTrack', array(
						'ajax'  => admin_url( 'admin-ajax.php' ),
						'nonce' => wp_create_nonce( 'rtp_track' ),
				) );
		}

		/** Track views */
		public static function track() {
				check_ajax_referer( 'rtp_track', 'nonce' );

				$id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
				if ( ! $id ) {
						wp_send_json_error( array( 'msg' => 'invalid id' ) );
				}

				$views = (int) get_post_meta( $id, RTP_CPT::META_VIEWS, true );
				$views++;
				update_post_meta( $id, RTP_CPT::META_VIEWS, $views );

				wp_send_json_success( array( 'views' => $views ) );
		}
}

RTP_Tracker::init();