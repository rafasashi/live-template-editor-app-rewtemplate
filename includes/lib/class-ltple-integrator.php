<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class LTPLE_Integrator_Boilerplate_Class extends LTPLE_Client_Integrator {
	
	public function appConnect(){

		$fields = $this->parent->apps->parse_url_fields($this->resourceUrl,'boilerplate-slug_' . $this->term->slug . '_');
		
		if( isset($_POST['boilerplate-slug_is_admin']) && $_POST['boilerplate-slug_is_admin'] == 'on' ){

			$terms = array();
			
			foreach($fields as $k => $field){
				
				if( !empty($_POST[$field['id']]) ){
					
					$value = $_POST[$field['id']];
					
					$terms[$k] 		= $value;
					$this->data[$k] = $value;
				}
				else{
					
					$terms = array();
					
					$this->message .= '<div class="alert alert-warning">';
						
						$this->message .= 'A field is missing...';
							
					$this->message .= '</div>';	
			
					break;
				}
			}
		}
		elseif(!empty($_POST)){
			
			$terms = array();
			
			$this->message .= '<div class="alert alert-warning">';
				
				$this->message .= 'You must be the admin of this resource...';
					
			$this->message .= '</div>';					
		}
		
		$outputForm = true;
			
		if( !empty($terms) ){

			// check is valid resource
			
			$resourceUrl = $this->resourceUrl;
			
			foreach($terms as $k => $v){	
			
				$resourceUrl = str_replace('{'.$k.'}',$v,$resourceUrl);
			}
			
			$ch = curl_init($resourceUrl);
			curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
			curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT,10);
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$output = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if( $httpcode >= 400 ){
				
				$this->message .= '<div class="alert alert-warning">';
					
					$this->message .= 'This resource couldn\'t be found...';
						
				$this->message .= '</div>';						
			}
			else{
				
				$outputForm = false;
				
				$this->data['resource'] = urlencode($resourceUrl);
			
				$app_title = $this->term->slug . ' - ' . implode('_',$terms);	

				$app_item = get_page_by_title( $app_title, OBJECT, 'user-app' );
				
				if( empty($app_item) ){

					// create app item
					
					$app_id = wp_insert_post(array(
					
						'post_title'   	 	=> $app_title,
						'post_status'   	=> 'publish',
						'post_type'  	 	=> 'user-app',
						'post_author'   	=> $this->parent->user->ID
					));
					
					if(is_numeric($app_id)){
						
						wp_set_object_terms( $app_id, $this->term->term_id, 'app-type' );
						
						// hook connected app
						
						do_action( 'ltple_' . str_replace( '-', '_', $this->term->slug ) . '_account_connected');
						
						$this->parent->apps->newAppConnected();
						
						$message = '<div class="alert alert-success" style="margin-bottom:0;">';
							
							$message .= 'Congratulations, you have successfully connected your ' . $this->term->name . ' account!';
								
						$message .= '</div>';
					}
					else{

						$message = '<div class="alert alert-warning" style="margin-bottom:0;">';
							
							$message .= 'Something went wrong...';
								
						$message .= '</div>';					
					}
				}
				else{

					$app_id = $app_item->ID;
					
					$message = '<div class="alert alert-info" style="margin-bottom:0;">';
						
						$message .= 'This app is already connected...';
							
					$message .= '</div>';
				}
				
				$this->parent->session->update_user_data('message',$message);

				// update app item
					
				update_post_meta( $app_id, 'appData', json_encode($this->data,JSON_PRETTY_PRINT));
			}				
		}
		
		if( $outputForm ){
			
			// output form
			
			$input = $this->resourceUrl;
			
			foreach($fields as $k => $field){			
				
				$input = str_replace('{'.$k.'}',' '.$this->parent->admin->display_field( $field, false, false ).' ',$input);				
			}

			$this->message .= '<form action="' . $this->parent->urls->current . '" method="post">';
				
				$this->message .= '<div class="col-xs-8">';
				
					$this->message .= '<h2>Add '.ucfirst($this->term->name).' account</h2>';
				
					$this->message .= '<div class="well form-group">';
				
						$this->message .= '<label class="input-group">';
						
							$this->message .= 'Account url';
						
						$this->message .= '</label>';					
				
						$this->message .= $input;
						
						$this->message .= '<div class="row">';
							
							$this->message .= '<div class="col-xs-6 text-left" style="margin-top:10px;">';
								
								$this->message .= $this->parent->admin->display_field( array(
								
									'type'				=> 'checkbox',
									'id'				=> 'boilerplate-slug_is_admin',
									'style'				=> 'width:15px;height:15px;float:left;',
									'description'		=> '',
									
						
								), false, false );
								
								$this->message .= 'I am the admin of this resource';
								
							$this->message .= '</div>';
							
							$this->message .= '<div class="col-xs-6 text-right" style="margin-top:10px;">';
							
								$this->message .= '<button class="btn btn-sm btn-primary" type="submit">Connect</button>';
							
							$this->message .= '</div>';
							
						$this->message .= '</div>';
						
					$this->message .= '</div>';
					
				$this->message .= '</div>';
				
			$this->message .= '</form>';				
		}
	}
}

