<?php

// Create our custom group and fields
return array(
  0 => array(
    'entity' => 'CustomGroup',
    'name' => 'participantfields_participant_info',
    'update' => 'never',
    'params' => array (
      'version' => 3,
      'is_active' => 1,
      'name' => 'participantfields_participant_info',
      'title' => 'Participant Info',
      'extends' => 'Participant',
      'extends_entity_column_id' => '2',
      'style' => 'inline',
      'collapse_display' => '0',
      'is_active' => '1',
      'is_multiple' => '0',
      'collapse_adv_display' => '0',
      'is_reserved' => '0',
      'is_public' => '1',
      'api.custom_field.create' => array(
        array(
          'custom_group_id' => '$value.id',
          'name' => 'participantfields_dietary_restrictions',
          'label' => 'Dietary Restrictions',
          'data_type' => 'String',
          'html_type' => 'Text',
          'help_post' => 'List all restrictions',
          'is_searchable' => '1',
          'is_search_range' => '0',
          'weight' => '20',
          'is_active' => '1',
          'is_view' => '0',
          'in_selector' => '0'
        ),
        array(
          'custom_group_id' => '$value.id',
          'label' => 'Child Care Needed',
          'name' => 'participantfields_child_care_needed',
          'data_type' => 'String',
          'html_type' => 'Text',
          "help_post" => "Enter names and ages of children",
          'is_required' => '0',
          'is_searchable' => '1',
          'is_search_range' => '1',
          'weight' => '10',
          'is_active' => '1',
          'is_view' => '0',
          'in_selector' => '0'
        ),
      ),
    ),
  ),
);
