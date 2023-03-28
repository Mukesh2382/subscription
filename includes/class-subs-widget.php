<?php
class Subscription_Widget extends WP_Widget
		{

			// Constructor function
			public function __construct()
			{
				$widget_options = array(
					'classname' => 'subscription_widget',
					'description' => 'A widget for subscribing to our newsletter'
				);
				parent::__construct('subscription_widget', 'Subscription Widget', $widget_options);
			}

			// Output the widget content on the front-end
			public function widget($args, $instance)
			{
				// Code to output the widget HTML goes here
				subscribe_me_callback();
			}

			// Output the widget form in the admin area
			public function form($instance)
			{
				// Code to output the widget form HTML goes here
			}

			// Handle saving the widget options
			public function update($new_instance, $old_instance)
			{
				// Code to handle saving the widget options goes here
			}
		}