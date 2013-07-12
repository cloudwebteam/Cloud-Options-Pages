<?php
class LL_User  {
	private static $instance ;
	public function init(){	
		if ( ! self::$instance ){
			self::$instance = new self() ;
		}
		return self::$instance ;
	}

	protected $ID ;
	public $logged_in = false ;
	protected $fan_clubs = array() ;
	protected $wallet = array() ;
	protected $form_args ;
	protected $data ; 
	protected $capabilities ;
	private function __construct( ){
		$current_user = wp_get_current_user() ;
		if ( $current_user->ID ){
			$this->ID = $current_user->ID ;
			$this->logged_in = true ;
			$this->data = $current_user->data; 
			$this->capabilities = $current_user->caps; 
		}
		$this->setup_ajax_functions() ;
	}
	protected function meta( $key ){

		return get_user_meta( $this->ID, $key, true );
	}
	protected function get_avatar(){
		$avatar_URL = get_user_meta( $this->ID, 'avatar', true ) ;			
		if ( $avatar_URL ){
			return '<img src="'.$avatar_URL.'" />' ;
		} else {
			$theme_options = get_option( 'll_options' );
			$avatar_ID = isset( $theme_options["user"]["default_avatar"] ) ? json_decode( $theme_options["user"]["default_avatar"], true ) : '' ;
			$avatar_ID = isset( $avatar_ID['media'] ) ? $avatar_ID['media'] : '' ; 
				
			if( $avatar_ID ){
				$image_info = wp_get_attachment_image_src( $avatar_ID, 'thumb' ); 

				$default_avatar = $image_info[0] ;
			} else {
				$default_avatar = false;
			}		
			$avatar_thumb = get_avatar( $this->user_email, $size = '96', $default_avatar  ) ; 
			return $avatar_thumb ;	
		}
	}
	protected function get_fan_clubs(){
		$fan_clubs = array() ;		
		if ( is_array( $this->fan_club_ids ) ){
			$db_results = LL::get_clubs( array( 'clubs' => $this->fan_club_ids ) ) ;

			foreach( $db_results as $fan_club ){
				$fan_clubs[ $fan_club->ID ] = $fan_club ; 
			}
		}
		return $fan_clubs ;
	}
	protected function get_wallet_perks(){
		$fan_club_wallet_perks = get_user_meta( $this->ID, 'wallet', true )	;
		return $fan_club_wallet_perks ;
	}
	
	/***====================================================================================================================================
			PUBLIC FUNCTIONS
		==================================================================================================================================== ***/
	public function get( $property ){
		if ( ! $this->logged_in ){
			return false; 
		} 

		switch ( $property ){
 
			case 'ID' :
				return $this->ID; 
			case  'avatar' : 
				return $this->get_avatar(); 
			case 'fan_club_ids' : 
				return $this->meta( 'fan_clubs' ); 
			case 'fan_clubs' : 
				return $this->get_fan_clubs() ; 
			case 'wallet' : 
				return $this->get_wallet_perks(); 
			default : 

				if ( isset( $this->data->$property ) ){
				
					return $this->data->$property ; 
				}
				return $this->meta( $property ); 
		}
		
	}	
	public function in_fan_club( $club_id = '' ){
		if ( ! $club_id ){
			global $post ; 
			$club_id = $post->ID ;
		} 

		return isset( $this->fan_club_ids ) && is_array( $this->fan_club_ids ) ? in_array( $club_id, $this->fan_club_ids ) : false ;
	}
	public function has_in_wallet( $perk_id ){
		if ( is_array( $this->wallet ) )
			return in_array( $perk_id, $this->wallet ) ;
		else
			return false;
	}
	public function get_form_args( $form_name ){
		switch ( $form_name ){
			case 'member-account' : 
				return array( 
					'title' => 'My account', 
					'ajax' => true, 
					'fields' => array( 
						'first_name' => array( 
							'title' => 'First name',
							'default' => $this->get( 'first_name' ), 
							'required' => 'Please enter your first name'
						), 
						'last_name' => array( 
							'title' => 'Last name', 
							'default' => $this->get( 'last_name' ), 					
							'required' => 'Please enter your last name'
						),
						'nickname' => array( 
							'title' => 'Nickname',
							'default' => $this->get( 'nickname' ), 	
							'description' => 'If provided, this is what we will call you on Loving Local.'									
						),
						'user_email' => array(
							'title' => 'Email',				
							'validate' => 'email', 
							'default' => $this->get('user_email'),
							'required' => 'We need this!',
							'error' => 'Seems invalid.'					
						),
						'gender' => array(
							'title'	=> 'Gender',
							'type'	=> 'select',
							'options'=> array(
								'm' => 'Male',
								'f' => 'Female'
							),
							'default'	=> $this->get( 'gender' ),
							'description'=> '(optional, but helps us to better match suggestions for you!)'
						),
						'birthday'	=> array(
							'title'	=> 'Birthday',
							'type'	=> 'date',							
							'description'=> '(optional, but you\'ll rake in special perks on your birthday from most Fan Clubs!)',
							'default'	=> $this->get( 'birthday' )
						)
					)
				);  
				break;
			case 'member-notifications' :
				$notification_pref =  $this->get( 'notification_pref' ) ;
				$newsletter_pref =  $this->get( 'newsletter_settings' ) ; 
				$notification_pref = $notification_pref ? $notification_pref : 'w';
				$newsletter_pref = $newsletter_pref ? $newsletter_pref : 'w';
				return array(
					'title' => 'My Notification Preferences',
					'fields' => array( 
						'notification_pref' => array(
							'title'	=> 'Perk Notifications',
							'type'	=> 'radio',
							'options'=> array(
								'r' => '<strong>REAL-TIME</strong>: Send email notifications immediately when my Fan Clubs offer new Perks',
								'd' 	=> '<strong>DAILY</strong>: Send an email summary once per day of my Fan Clubs new Perks',
								'w' 	=> '<strong>WEEKLY</strong>: Send an email summary once per week of my Fan Clubs new Perks',
								'n' 	=> '<strong>NEVER</strong>: Don\'t send any email notifications when my Fan Clubs offer new Perks'
							),
							'default'	=> $notification_pref,
						),
						'newsletter_settings' => array(
							'title'	=> 'LovingLocal Newsletter',
							'type'	=> 'radio',
							'options'=> array(
								'w' 	=> '<strong>WEEKLY</strong>: I want to receive the newsletter highlighting the latest and greatest Fan Clubs & Perks once per week',
								'm' 	=> '<strong>MONTHLY</strong>: I want to receive the newsletter highlighting the latest and greatest Fan Clubs & Perks once per month',
								'n' 	=> '<strong>NEVER</strong>: I don\'t want to receive the newsletter highlighting the latest and greatest Fan Clubs & Perks',
							),
							'default'	=> $newsletter_pref
						)
					)
				) ; 
				break;
		}

		return $form_args ;

	}
	public function form( $form_name ){	
		new Form( $this->get_form_args( $form_name ) );
	}
	public function save_form( $form_data , $form_id ){
	
		switch( $form_id )
		{
			case 'account_form' :
				$user_data_fields = array( 
					//'display_name' => 'display_name',
					'email' => 'user_email'
				) ;
				$user_meta_fields = array( 
					'first_name' 	=> 'first_name',
					'last_name' 	=> 'last_name',			
					'nickname' 		=> 'nickname',
					'gender'		=> 'gender',
					'birthday'		=> 'birthday'
				) ;
				$wp_data_to_update = array() ;
				foreach( $form_data as $field_name => $field_data ){
		
					if ( isset( $user_data_fields[ $field_name ] ) ){
						$wp_data_to_update[ $user_data_fields[ $field_name ] ] = $field_data ;
					} else if ( isset( $user_meta_fields[ $field_name ] ) ){
						update_user_meta( $this->ID, $user_meta_fields[$field_name] , $field_data) ;
					}
				}
		
				if ( sizeof( $wp_data_to_update ) > 0 ){
		
					$wp_data_to_update[ 'ID'] = $this->ID ;			
					wp_update_user( $wp_data_to_update ) ;
				}
				break;
			case 'user_notification_settings_form' :
				foreach( $form_data as $field_name => $field_data ){
					if ( $field_name == 'form_id' ){
						continue;
					} else {
						update_user_meta( $this->ID, $field_name , $field_data) ;
					}
				}
				break;
		}
		
		return true ;
	}
	/***====================================================================================================================================
			AJAX FUNCTIONS
		==================================================================================================================================== ***/	
	protected function setup_ajax_functions(){
		add_action( 'wp_ajax_user_login' , array( $this, 'login_user' ) ) ;
		add_action( 'wp_ajax_nopriv_user_login' , array( $this, 'login_user' ) ) ;
		
		add_action( 'wp_ajax_user_register' , array( $this, 'register_user' ) ) ;
		add_action( 'wp_ajax_nopriv_user_register' , array( $this, 'register_user' ) ) ;		
		
		add_action( 'wp_ajax_user_join_fan_club' , array( $this, 'join_fan_club' ) ) ;
		add_action( 'wp_ajax_nopriv_user_join_fan_club' , array( $this, 'join_fan_club' ) ) ;		

		add_action( 'wp_ajax_user_leave_fan_club' , array( $this, 'leave_fan_club' ) ) ;
		add_action( 'wp_ajax_nopriv_user_leave_fan_club' , array( $this, 'leave_fan_club' ) ) ;				
		
		add_action( 'wp_ajax_update_club_notification_pref' , array( $this, 'update_club_notification_pref' ) ) ;
		add_action( 'wp_ajax_nopriv_update_club_notification_pref' , array( $this, 'update_club_notification_pref' ) ) ;				
		
		add_action( 'wp_ajax_user_add_to_wallet' , array( $this, 'add_to_wallet' ) ) ;
		add_action( 'wp_ajax_nopriv_user_add_to_wallet' , array( $this, 'add_to_wallet' ) ) ;				
		
		add_action( 'wp_ajax_user_remove_from_wallet' , array( $this, 'remove_from_wallet' ) ) ;
		add_action( 'wp_ajax_nopriv_user_remove_from_wallet' , array( $this, 'remove_from_wallet' ) ) ;		

		add_action( 'wp_ajax_user_ask_for_perks' , array( $this, 'ask_for_perks' ) ) ;
		add_action( 'wp_ajax_nopriv_user_ask_for_perks' , array( $this, 'ask_for_perks' ) ) ;	
	}
	public function login_user(){
		$fields = array(
			'user_password' => array(
				'name' => 'user_password',
				'type' => 'text',
				'required' => true,
				'error' => 'Please enter a password' 
			),
			'user_email' => array(
				'name' => 'user_email',
				'required' => true,
				'validate' => 'email',
				'type' => 'email',
				'error' => 'Please enter a valid email address.'
			), 
			'remember' => array(
				'name' => 'remember'
			)
		) ; 
		$form_submission = array();
		foreach( $_POST['form'] as $field ){
			$form_submission[$field['name'] ] = $field['value'] ;
		}
		$results = Validator::validate( $form_submission, $fields );
		if ( !$results['success'] ){
			$output['errors'] = $results['errors'] ;
		} else {
			$user_object = get_user_by( 'email', $results['to_save']['user_email'] ) ;
			if ( $user_object ){
				$remember = isset( $results['to_save']['remember'] );
				$creds = array( 
					'user_login' => $user_object->user_login,
					'user_password' => $results['to_save']['user_password'],
					'remember' => $remember
				) ;
				$log_in_response = wp_signon( $creds, true ) ;
				if( get_class( $log_in_response ) == 'WP_User' ){ 
					$output = true ;
				} else {
					$output['status'] = 'Incorrect username or password' ;
				}
			} else {
				$output['status'] = 'Incorrect username or password' ;
			}

		}
		echo json_encode($output) ;
		die ;
	}
	public function register_user(){
		$fields = array(
			'user_password' => array(
				'name' => 'user_password',
				'type' => 'text',
				'required' => true,
				'error' => 'Please enter a password' 
			),
			'user_email' => array(
				'name' => 'user_email',
				'required' => true,
				'validate' => 'email',
				'type' => 'email',
				'error' => 'Please enter a valid email address.'
			), 
			'first_name' => array(
				'required' => true
			),
			'last_name' => array(
				'required' => true
			),
			'city' => array(
				'required' => true
			)			
		) ; 
		$form_submission = array();
		foreach( $_POST['form'] as $field ){
			$form_submission[$field['name'] ] = $field['value'] ;
		}
		$results = Validator::validate( $form_submission, $fields );
		if ( !$results['success'] ){
			$output['errors'] = $results['errors'] ;
		} else {
			$user_data = $results['to_save'] ;
			$new_user_id = wp_insert_user( array(
				'user_pass' => $user_data['user_password'], 
				'user_login' => $user_data['user_email'],
				'user_email' => $user_data['user_email']			
			) ) ;
			update_user_meta( $new_user_id, 'city', $user_data['city'] );
			update_user_meta( $new_user_id, 'first_name', $user_data['first_name'] );
			update_user_meta( $new_user_id, 'last_name', $user_data['last_name'] );
			update_user_meta( $new_user_id, 'nickname', $user_data['first_name'] );
			if ( is_integer( $new_user_id ) ){

				$creds = array( 
					'user_login' => $user_data['user_email'],
					'user_password' => $user_data['user_password'],
					'remember' => true
				) ;
				$log_in_response = wp_signon( $creds, true ) ;
				if( get_class( $log_in_response ) == 'WP_User' ){ 
					$output = true ;
				} else {
					$output['status'] = 'Failed to sign in' ;
				}
			} else {
				$output['status'] = 'Failed to create new user' ;
			}

		}
		echo json_encode($output) ;
		die ;
	}	
	public function join_fan_club(){
		if ( $this->logged_in ){	
			$business_id = $_POST['business_id'] ;
			$current_fan_clubs = get_user_meta( $this->ID, 'fan_clubs', true ) ;
			if( $current_fan_clubs && $this->in_fan_club( $business_id ) ){
				$response = array( 
					'message' => 'You are already in this fan club!',
					'type' => 'notice' 
				) ;			
			} else {
				if ( ! $current_fan_clubs ){
					$current_fan_clubs = array() ;
				}
				$business = get_post( $business_id ) ;
				$current_fan_clubs[] = $business_id ; 
				if ( update_user_meta( $this->ID, 'fan_clubs', $current_fan_clubs ) ){
					$response = array( 
						'message' => 'You joined <b>' .$business->post_title .'\'s</b> fan club.',
						'type' => 'success' 
					) ;
				} else {
					$response = array( 
						'message' => 'Something went wrong with joining fan club',
						'type' => 'error' 
					) ;				
				}
			
			}
			
		} else {
			$response = array( 
				'message' => 'Please login/signup to join this Fan Club!',
				'type' => 'notice',
				'action' => 'login'
			) ;						
		}
		echo json_encode( $response ) ; 
		die ;
	}
	public function leave_fan_club(){
		$club_id = $_POST['business_id'] ; 
		if( $this->in_fan_club( $club_id ) ) {
			$club = get_post( $club_id ) ;
			foreach( $this->wallet as $index => $perk_id ){
				$perk_club_id = get_post_meta( $perk_id, 'club', true ); 
				if ( $perk_club_id == $club_id ){
					unset( $this->wallet[ $index ] ); 
				}
			}
			update_user_meta( $this->ID, 'wallet', $this->wallet ) ;	
			
			$key = array_search( $club_id, $this->fan_club_ids ) ;
			unset( $this->fan_club_ids[$key] ) ; 
			if ( update_user_meta( $this->ID, 'fan_clubs', $this->fan_club_ids ) ){		
				$response = array( 
					'message' => 'You left <b>' .$club->post_title .'\'s</b> fan club.',
					'type' => 'success' 
				) ;
			} else {
				$response = array( 
					'message' => 'Something went wrong with leaving fan club',
					'type' => 'error' 
				) ;				
			}
		} else {
			$response = array( 
				'message' => 'You were not in fan club!',
				'type' => 'notice' 
			) ;						
		}
		echo json_encode( $response ) ; 
		die ;		
	}	
	public function update_club_notification_pref(){
		$club_id = $_POST['club_id'] ; 
		$preference = $_POST['preference'] ; 
		
		$current_preferences = get_user_meta( $this->ID, 'club_notification_prefs', true ); 
		$current_preferences[ $club_id ] = $preference ; 
		if ( update_user_meta( $this->ID, 'club_notification_prefs', $current_preferences ) ){
			$response = array( 
				'message' => 'You updated your notification preference!',
				'type' => 'success' 
			) ;			
		} else {
			$response = array( 
				'message' => 'Something went wrong. Please try again later.',
				'type' => 'notice' 
			) ;					
		}
		echo json_encode( $response ) ; 
		die ;			
	}
	public function add_to_wallet(){
		if ( $this->logged_in ){	
			$perk_id = $_POST['perk_id'] ;  
			if( $this->in_wallet( $perk_id ) ) {
				$response = array( 
					'message' => 'This perk is already in your wallet!',
					'type' => 'notice' 
				) ;	
			} else {
				$club_id = get_post_meta( $perk_id, 'club', true ); 
				if ( ! $this->in_fan_club( $club_id ) ){
					$this->fan_club_ids[] = $club_id ; 
					$joined_club = update_user_meta( $this->ID, 'fan_clubs', $this->fan_club_ids ) ;
					$this->wallet[] = $perk_id ; 
					if ( update_user_meta( $this->ID, 'wallet', $this->wallet ) ){
						$club = get_post( $club_id ) ;
						$message = 'You joined <b>'.$club->post_title.'\'s</b> fan club, and added the perk to your wallet.' ;
						$response = array( 
							'message' => $message,
							'type' => 'success', 
							'action' => 'joined_club', 
							'club_id' => $club->ID
						) ;
					} else {
						$response = array( 
							'message' => 'Something went wrong with adding perk to your wallet.',
							'type' => 'error' 
						) ;				
					}					
				} else {
					$this->wallet[] = $perk_id ; 
					if ( update_user_meta( $this->ID, 'wallet', $this->wallet ) ){
						$perk = get_post( $perk_id ) ;
						$message = 'You added <b>'.$perk->post_title.'\'s</b> to your wallet.' ;
						$response = array( 
							'message' => $message,
							'type' => 'success' 
						) ;
					} else {
						$response = array( 
							'message' => 'Something went wrong with adding perk to your wallet.',
							'type' => 'error' 
						) ;				
					}				
				}
				
			}
		} else {
			$response = array( 
				'message' => 'Please login/signup to add this to your Wallet!',
				'type' => 'notice',
				'action' => 'login'
			) ;			
		}
		echo json_encode( $response ) ; 
		die ;
	}
	public function remove_from_wallet(){
		$perk_id = $_POST['perk_id'] ;  
					
		if( $this->in_wallet( $perk_id ) ) {
			$key = array_search( $perk_id, $this->wallet ) ;
			unset( $this->wallet[$key] ) ;
			if ( update_user_meta( $this->ID, 'wallet', $this->wallet ) ){		
				$perk = get_post( $perk_id ) ;
				$response = array( 
					'message' => 'Removed <b>' .$perk->post_title .'</b> from your wallet.',
					'type' => 'success' 
				) ;
			} else {
				$response = array( 
					'message' => 'Something went wrong with removing perk from your wallet.',
					'type' => 'error' 
				) ;				
			}		
		} else {
			$response = array( 
				'message' => 'This perk wasn\'t in your wallet!',
				'type' => 'notice' 
			) ;			
		}
		echo json_encode( $response ) ; 
		die ;
	}		
	public function ask_for_perks(){
		if ( $this->logged_in ){	
			$club_id = $_POST['club_id'] ;
			
			if( $this->in_fan_club( $club_id ) ) {
				$response = array( 
					'message' => 'Successfully sent email! (note, we didn\'t)',
					'type' => 'success' 
				) ;	
			} else {
				$response = array( 
					'message' => 'Successfully sent email! (note, we didn\'t)<br /> and added you to the fan club so that you can hear about updates (not really...)',
					'type' => 'success'
				) ;
			}
		} else {
			$response = array( 
				'message' => 'Please login/signup to add this to your Wallet!',
				'type' => 'notice',
				'action' => 'login'
			) ;			
		}
		echo json_encode( $response ) ; 
		die ;
	}
}

