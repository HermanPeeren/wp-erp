<?php

/**
 * Get company work days
 *
 * @param  int  $company_id
 *
 * @return array
 */
function erp_hr_get_work_days() {
    $default = array(
        'mon' => 8,
        'tue' => 8,
        'wed' => 8,
        'thu' => 8,
        'fri' => 8,
        'sat' => 0,
        'sun' => 0
    );

    $option_key = 'erp_hr_work_days';

    return get_option( $option_key, $default );
}

function erp_hr_get_work_days_between_dates( $start_date, $end_date ) {
    $between_dates = erp_extract_dates( $start_date, $end_date );

    if ( is_wp_error( $between_dates ) ) {
        return $between_dates;
    }

    $dates     = array( 'days' => array(), 'total' => 0 );
    $work_days = erp_hr_get_work_days();

    foreach ($between_dates as $date) {
        $key       = strtolower( date( 'D', strtotime( $date ) ) );
        $is_holidy = ( $work_days[$key] === 0 ) ? true : false;

        $dates['days'][] = array(
            'date'    => $date,
            'count' => (int) ! $is_holidy
        );

        if ( ! $is_holidy ) {
            $dates['total'] += 1;
        }
    }

    return $dates;
}

/**
 * sort parents before children
 *
 * @param array   $objects input objects with attributes 'id' and 'parent'
 * @param array   $result  (optional, reference) internal
 * @param integer $parent  (optional) internal
 * @param integer $depth   (optional) internal
 *
 * @return array           output
 */
function erp_parent_sort( array $objects, array &$result=array(), $parent=0, $depth=0 ) {
    foreach ($objects as $key => $object) {
        if ($object->parent == $parent) {
            $object->depth = $depth;
            array_push($result, $object);
            unset($objects[$key]);
            erp_parent_sort($objects, $result, $object->id, $depth + 1);
        }
    }
    return $result;
}
