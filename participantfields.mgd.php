<?php
return [
  [
    'name' => 'participantfields_participant_info', 
    'entity' => 'CustomGroup', 
    'cleanup' => 'unused', 
    'update' => 'unmodified', 
    'params' => [
      'version' => 4, 
      'match' => ['name'],
      'values' => [
        'name' => 'participantfields_participant_info', 
        'title' => 'Participant Info', 
        'extends' => 'Participant', 
        'extends_entity_column_id:name' => 'ParticipantEventName', 
        'extends_entity_column_value' => NULL, 
        'style' => 'Inline', 
        'collapse_display' => FALSE, 
        'help_pre' => NULL, 
        'help_post' => NULL, 
        'weight' => 1, 
        'is_active' => TRUE, 
        'is_multiple' => FALSE, 
        'min_multiple' => NULL, 
        'max_multiple' => NULL, 
        'collapse_adv_display' => FALSE, 
        'created_date' => NULL, 
        'is_reserved' => FALSE, 
        'is_public' => TRUE, 
        'icon' => NULL,
      ],
    ],
  ], 
  [
    'name' => 'participantfields_child_care_needed', 
    'entity' => 'CustomField', 
    'cleanup' => 'unused', 
    'update' => 'unmodified', 
    'params' => [
      'version' => 4, 
      'match' => ['name'],
      'values' => [
        'custom_group_id.name' => 'participantfields_participant_info', 
        'name' => 'participantfields_child_care_needed', 
        'label' => 'Child Care Needed', 
        'data_type' => 'String', 
        'html_type' => 'Text', 
        'default_value' => NULL, 
        'is_required' => FALSE, 
        'is_searchable' => TRUE, 
        'is_search_range' => FALSE, 
        'help_pre' => NULL, 
        'help_post' => 'Enter names and ages of children', 
        'mask' => NULL, 
        'attributes' => NULL, 
        'javascript' => NULL, 
        'is_active' => TRUE, 
        'is_view' => FALSE, 
        'options_per_line' => NULL, 
        'text_length' => 32, 
        'start_date_years' => NULL, 
        'end_date_years' => NULL, 
        'date_format' => NULL, 
        'time_format' => NULL, 
        'note_columns' => NULL, 
        'note_rows' => NULL, 
        'option_group_id' => NULL, 
        'serialize' => 0, 
        'filter' => NULL, 
        'in_selector' => FALSE,
      ],
    ],
  ], 
  [
    'name' => 'participantfields_dietary_restrictions', 
    'entity' => 'CustomField', 
    'cleanup' => 'unused', 
    'update' => 'unmodified', 
    'params' => [
      'version' => 4, 
      'match' => ['name'],
      'values' => [
        'custom_group_id.name' => 'participantfields_participant_info', 
        'name' => 'participantfields_dietary_restrictions', 
        'label' => 'Dietary Restrictions', 
        'data_type' => 'String', 
        'html_type' => 'Text', 
        'default_value' => NULL, 
        'is_required' => FALSE, 
        'is_searchable' => TRUE, 
        'is_search_range' => FALSE, 
        'help_pre' => NULL, 
        'help_post' => 'List all restrictions', 
        'mask' => NULL, 
        'attributes' => NULL, 
        'javascript' => NULL, 
        'is_active' => TRUE, 
        'is_view' => FALSE, 
        'options_per_line' => NULL, 
        'text_length' => NULL, 
        'start_date_years' => NULL, 
        'end_date_years' => NULL, 
        'date_format' => NULL, 
        'time_format' => NULL, 
        'note_columns' => NULL, 
        'note_rows' => NULL, 
        'option_group_id' => NULL, 
        'serialize' => 0, 
        'filter' => NULL, 
        'in_selector' => FALSE,
      ],
    ],
  ], 
  [
    'name' => 'participantfields_update_event_invite_response',
    'entity' => 'UFGroup', 
    'cleanup' => 'unused', 
    'update' => 'unmodified', 
    'params' => [
      'version' => 4, 
      'match' => ['name'],
      'values' => [
        'is_active' => TRUE, 
        'group_type' => [
          'Participant',
        ],
        'name' => 'participantfields_update_event_invite_response',
        'title' => 'Update Participant Info',
        'description' => 'Powerbase profile for updating responses to invitations',
        'frontend_title' => NULL,
        'help_pre' => NULL,
        'help_post' => NULL,
        'limit_listings_group_id' => NULL,
        'post_URL' => NULL,
        'add_to_group_id' => NULL,
        'add_captcha' => FALSE,
        'is_map' => FALSE,
        'is_edit_link' => FALSE,
        'is_uf_link' => FALSE,
        'is_update_dupe' => FALSE,
        'cancel_URL' => NULL,
        'is_cms_user' => FALSE,
        'notify' => NULL,
        'is_reserved' => FALSE,
        'is_proximity_search' => FALSE,
        'cancel_button_text' => NULL,
        'submit_button_text' => NULL,
        'add_cancel_button' => TRUE,
      ],
    ],
  ], 
  [
    'name' => 'uffield_participantfields_child_care_needed',
    'entity' => 'UFField',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'match' => ['field_name:name'],
      'values' => [
        'uf_group_id.name' => 'participantfields_update_event_invite_response',
        'field_name:name' => 'participantfields_participant_info.participantfields_child_care_needed',
        'is_active' => TRUE,
        'is_view' => FALSE,
        'is_required' => FALSE,
        'help_post' => 'Enter names and ages of children',
        'help_pre' => NULL,
        'visibility' => 'User and User Admin Only',
        'in_selector' => FALSE,
        'is_searchable' => FALSE,
        'location_type_id' => NULL,
        'phone_type_id' => NULL,
        'website_type_id' => NULL,
        'label' => 'Child Care Needed',
        'field_type' => 'Participant',
        'is_reserved' => FALSE,
        'is_multi_summary' => FALSE,
      ],
    ],
  ],
  [
    'name' => 'uffield_participantfields_dietary_restrictions', 
    'entity' => 'UFField', 
    'cleanup' => 'unused', 
    'update' => 'unmodified', 
    'params' => [
      'version' => 4, 
      'match' => ['field_name:name'],
      'values' => [
        'uf_group_id.name' => 'participantfields_update_event_invite_response',
        'field_name:name' => 'participantfields_participant_info.participantfields_dietary_restrictions', 
        'is_active' => TRUE, 
        'is_view' => FALSE, 
        'is_required' => FALSE, 
        'help_post' => 'List all restrictions', 
        'help_pre' => NULL, 
        'visibility' => 'User and User Admin Only', 
        'in_selector' => FALSE, 
        'is_searchable' => FALSE, 
        'location_type_id' => NULL, 
        'phone_type_id' => NULL, 
        'website_type_id' => NULL, 
        'label' => 'Dietary Restrictions', 
        'field_type' => 'Participant', 
        'is_reserved' => FALSE, 
        'is_multi_summary' => FALSE,
      ],
    ],
  ],
];
