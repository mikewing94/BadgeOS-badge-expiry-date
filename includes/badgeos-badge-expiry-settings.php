<?php
class Badgeos_Badge_Expiry_Settings {
  
  const DEFAULT_NOTIFY_EMAIL_DAYS  = 7;
  const FIELD_PREFIX  = '_badgeos_badge_expiry_';
  static $fields  = array (
    'validity_1', 'validity_2', 'last_ladder_change_period', 'notify_email_days', 
  );
  
  protected $settings_page_name;
  
  public function __construct() {
    //add_filter( 'badgeos_get_achievement_earners_list_user', array($this, 'show_expiry_date'), 10, 2 );
    add_filter( 'badgeos_render_achievement', array($this, 'render_achievement'), 10, 2 );


    add_action('wp_ajax_update_badges_validity', array($this, 'update_badges_validity'));
    
    add_action( 'admin_menu', array($this, 'add_menu'));
    //add_action( 'wp_enqueue_scripts', array($this, 'add_script'));
    add_action( 'admin_enqueue_scripts', array($this, 'add_script'));
    
    //Cron
    add_action( 'badgeos_badge_expiry_event_hook', array($this, 'badge_epiry_cron_job') );
    //require_once '/www/dBug.php';
  }
  
/**
 * badgeos-badge-expiry main settings page output
 * @since  1.0
 * @return void
 */
  public function display() {
    //die(__line__);
    //require_once( get_home_path().'dBug.php' );
    //new dBug(get_option('cron'));
    $ajax_nonce = wp_create_nonce( __CLASS__ );
    $badges = $this->get_badges();
    $badges_expires = $this->get_badges_expiry_details();
    
    //global $wpdb, $userdata;
    //var_dump($userdata);
    //new dBug(badgeos_get_achievement_earners(7), '', 1);
    //new dBug(badgeos_get_achievement_earners_list(7), '', 1);
    //new dBug(badgeos_user_get_active_achievements(1), '', 1);
    //new dBug(badgeos_user_get_active_achievements(3), '', 1);
    //new dBug(badgeos_get_user_earned_achievement_types(3), '', 1);
    //new dBug(badgeos_get_user_earned_achievement_ids(3), '', 1);
    //new dBug(gmdate('Y-m-d H:i:s', badgeos_achievement_last_user_activity(7, 3)), '', 1);
    //badgeos_user_add_active_achievement(1, 7);
    //new dBug(badgeos_get_achievement_metas('_badgeos_achievements', 'badges'), '', 1);
    //new dBug(badgeos_get_unserialized_achievement_metas('_badgeos_achievements', 'badges'), '', 1);
    //new dBug(badgeos_get_user_earned_achievement_ids(1, array('badges')), '', 1);
    
    //new dBug($badges_expires, '', 1);
    //$this->badge_epiry_cron_job();
?>
  <script type="text/javascript">
    var ajax_nonce  = '<?php echo $ajax_nonce; ?>';
  </script>
	<div class="wrap" >
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e( 'BadgeOS badge expiry Settings', 'badgeos-badge-expiry' ); ?></h2>
    <?php if ( current_user_can( 'manage_options' ) ) { ?>
    <?php _e( 'BadgeOS badge expiry settings ', 'badgeos' ); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row" style="width:30%;">Badge</th>
        <td style="width:15%;">Validity Period 1</td>
        <td style="width:15%;">Validity Period 2</td>
        <td style="width:15%;">Validity Mitigator</td>
        <td style="width:15%;">Send email reminder<br />x days before expiry</td>
        <td></td>
      </tr>
    <?php 
        $validity_1_field = self::prefix_field('validity_1');
        $validity_2_field = self::prefix_field('validity_2');
        $last_ladder_change_period_field = self::prefix_field('last_ladder_change_period');
        $notify_email_days_field = self::prefix_field('notify_email_days');
        foreach ($badges as $badge) {
          $badges_expiry = isset($badges_expires[$badge->ID]) ? $badges_expires[$badge->ID] : $this->get_validity_defaults();
          $validity_1 = property_exists($badges_expiry, $validity_1_field) ? $badges_expiry->{$validity_1_field} : '';
          $validity_2 = property_exists($badges_expiry, $validity_2_field) ? $badges_expiry->{$validity_2_field} : '';
          $last_ladder_change_period = property_exists($badges_expiry, $last_ladder_change_period_field) ? $badges_expiry->{$last_ladder_change_period_field} : '';
          $notify_email_days = property_exists($badges_expiry, $notify_email_days_field) ? $badges_expiry->{$notify_email_days_field} : '';
    ?>
        <tr valign="top">
          <th scope="row"><label for="minimum_role"><?php echo $badge->post_title; ?></label></th>
          <td><input type="text" data-field_name="validity_1" name="validity_1[<?php echo $badge->ID; ?>]" id="validity_1[<?php echo $badge->ID; ?>]" value="<?php echo $validity_1; ?>" size="3" /></td>
          <td><input type="text" data-field_name="validity_2" name="validity_2[<?php echo $badge->ID; ?>]" id="validity_2[<?php echo $badge->ID; ?>]" value="<?php echo $validity_2; ?>" size="3" /></td>
          <td><input type="text" data-field_name="last_ladder_change_period" name="last_ladder_change_period[<?php echo $badge->ID; ?>]" id="last_ladder_change_period[<?php echo $badge->ID; ?>]" value="<?php echo $last_ladder_change_period; ?>" size="3" /></td>
          <td><input type="text" data-field_name="notify_email_days" name="notify_email_days[<?php echo $badge->ID; ?>]" id="notify_email_days[<?php echo $badge->ID; ?>]" value="<?php echo $notify_email_days; ?>" size="3" /></td>
          <td><input type="hidden" data-field_name="post_id" name="post_id[<?php echo $badge->ID; ?>]" id="post_id[<?php echo $badge->ID; ?>]" value="<?php echo $badge->ID; ?>" /><input type="button" name="badges_expiry_action[<?php echo $badge->ID; ?>]" id="badges_expiry_action[<?php echo $badge->ID; ?>]" value="Update" /></td>
        </tr>
    <?php } ?>
    </table>
	<?php } ?>
	</div>
  <div id="badgeos_badge_expiry_settings_loading" style="display: none;"><img src="<?php echo BADGEOS_BADGE_EXPIRY_URL . 'images/spinner.gif' ?>" alt="" /><!--<img src="<?php echo includes_url(); ?>js/thickbox/loadingAnimation.gif" alt="" />--></div>
<?php
  }
  
  public function update_badges_validity() {
    //sleep(5);
    global $wpdb, $userdata;
    if ( ! is_user_logged_in()) {
      return;
    }
    check_ajax_referer( __CLASS__, 'security' );
    $post_id  = isset($_POST['post_id']) ? $_POST['post_id']: '';
    if (empty($post_id)) return;
    foreach (self::$fields as $field_name) {
      if (isset($_POST[$field_name])) {
        $meta_key = self::prefix_field($field_name);
        add_post_meta( $post_id, $meta_key, $_POST[$field_name], true ) || update_post_meta( $post_id, $meta_key, $_POST[$field_name] );
      }
    }
  }
  
  public function get_badges() {
    global $wpdb;
    $data = array();
    $values = $wpdb->get_results( $wpdb->prepare("SELECT P.ID, P.post_title FROM {$wpdb->posts} P WHERE P.post_type = %s ", 'badges') );
    foreach ($values as $obj) {
      $data[$obj->ID] = $obj;
    }
    
    return $data;
  }
  
  public function get_badges_expiry_details() {
    global $wpdb;
    $data = array();
    $sql  = "SELECT PM.post_id, PM.meta_key, PM.meta_value FROM {$wpdb->postmeta} PM WHERE PM.meta_key IN (%s".str_repeat(", %s", count(self::$fields)-1).") ";
    $values = $wpdb->get_results(
      call_user_func_array(array($wpdb, 'prepare'), array_merge(array($sql), array_map(array('Badgeos_Badge_Expiry_Settings', 'prefix_field'), self::$fields)))
    );
    foreach ($values as $obj) {
      if ( ! isset($data[$obj->post_id])) {
        $data[$obj->post_id]  = new stdClass;
      }
      $data[$obj->post_id]->{$obj->meta_key} = $obj->meta_value;
    }
    
    return $data;
  }
  
  public function get_badgeos_achievements() {
    global $wpdb;
    $data = array();
    $data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->usermeta}  WHERE meta_key = '_badgeos_achievements' ", array() ) );

    return $data;
    //return badgeos_get_achievement_metas('_badgeos_achievements', 'badges');
    return badgeos_get_unserialized_achievement_metas('_badgeos_achievements', 'badges');
  }
  
  public function add_menu() {
    $minimum_role = function_exists('badgeos_get_manager_capability') ? badgeos_get_manager_capability() : '';
    
    $this->settings_page_name = add_menu_page( 'BadgeOS Badge Expiry', 'BadgeOS Badge Expiry', $minimum_role, 'badgeos_badge_expiry_settings', array($this, 'display') );
    
  }
  
  public function add_script($hook) {
    if ( $this->settings_page_name != $hook ) {
      return;
    }
    wp_enqueue_script( 'badgeos-badge-expiry', BADGEOS_BADGE_EXPIRY_URL . 'js/badgeos-badge-expiry.js', array(), '1.0', true );
    wp_enqueue_script(array('jquery-ui-core', 'jquery-ui-dialog'));
    wp_enqueue_style( 'badgeos-badge-expiry', BADGEOS_BADGE_EXPIRY_URL . 'css/jquery-ui.min.css' );
  }
  
  protected function check_validity_1($user, $badge_obj, $validity_settings) {
    //var_dump(__METHOD__);
    static $tz;
    static $today;
    if (empty($tz)) {
      $tz = new DateTimeZone('GMT');
    }
    if (empty($today)) {
      $today = new DateTime('now', $tz);
    }
    $achievement_date = clone $today;
    $achievement_date = $achievement_date->setTimestamp($badge_obj->date_earned);
    //new dBug($achievement_date->format('Y-m-d H:i:s'));
    
    $validity_1_field = self::prefix_field('validity_1');
    $validity_1 = (int) $validity_settings->{$validity_1_field};
    $expiry_date  = clone $achievement_date;
    $expiry_date->add(new DateInterval("P{$validity_1}D"));
    //echo 'Validity 1 - expiry_date';
    //new dBug($expiry_date->format('Y-m-d H:i:s'));
    if ($today < $expiry_date) {
      $date_args  = array (
        'today' => $today, 
        'expiry_date' => $expiry_date, 
      );
      $this->send_email_notification($user, $badge_obj, $validity_settings, $date_args);
      return true;
    }
    return false;
  }
  
  protected function check_validity_2($user, $badge_obj, $validity_settings) {
    //var_dump(__METHOD__);
    static $tz;
    static $today;
    if (empty($tz)) {
      $tz = new DateTimeZone('GMT');
    }
    if (empty($today)) {
      $today = new DateTime('now', $tz);
    }
    $achievement_date = clone $today;
    $achievement_date = $achievement_date->setTimestamp($badge_obj->date_earned);
    //new dBug($achievement_date->format('Y-m-d H:i:s'));
    
    $validity_2_field = self::prefix_field('validity_2');
    $validity_2 = (int) $validity_settings->{$validity_2_field};
    $expiry_date  = clone $achievement_date;
    //echo 'Validity 2 - expiry_date';
    $expiry_date->add(new DateInterval("P{$validity_2}D"));
    //new dBug($expiry_date->format('Y-m-d H:i:s'));
    if ($today > $expiry_date) {
      return false;
    }
    if ( ! $this->is_last_ladder_changed($badge_obj, $validity_settings)) {
      return false;
    }
    
    $date_args  = array (
      'today' => $today, 
      'expiry_date' => $expiry_date, 
    );
    $this->send_email_notification($user, $badge_obj, $validity_settings, $date_args);
    
    return true;
  }
  
  protected function is_last_ladder_changed($badge_obj, $validity_settings) {
    // Code for ladder change check goes here
    
    return true;
  }
  
  protected function check_validity($user, $badge_obj, $validity_settings) {
    if ( ! $this->check_validity_1($user, $badge_obj, $validity_settings)) {
      if ( ! $this->check_validity_2($user, $badge_obj, $validity_settings)) {
        return false;
      }
    }
    return true;
  }
  
  protected function send_email_notification($user, $badge_obj, $validity_settings, $date_args) {
    //echo __METHOD__;
    //new dBug($validity_settings, '', 1);
    $notify_email_date_field  = self::prefix_field('notify_email_days');
    $notify_email_date  = clone $date_args['expiry_date'];
    $notify_email_days  = (property_exists($validity_settings, $notify_email_date_field) && ! empty($validity_settings->{$notify_email_date_field})) ? $validity_settings->{$notify_email_date_field} : self::DEFAULT_NOTIFY_EMAIL_DAYS;
    $notify_email_date->sub(new DateInterval("P{$notify_email_days}D"));
    //new dBug($notify_email_date->format('Y-m-d H:i:s'));
    
    $date_diff  = $notify_email_date->diff($date_args['today']);
    //new dBug($date_diff->format('%a'));
    if ($date_diff && $date_diff->format('%a') == 0) {
      $subject  = $user->user_email.' Badge Expiry';
      $message  = 'Your badge will expire on '.$date_args['expiry_date']->format('d-m-Y H:i:s');
      $this->send_email($user->user_email, $subject, $message);
    }
  }
  
  protected function send_email($to, $subject, $message) {
    //echo __METHOD__.'<br />';
    //echo $to.'<br />';
    wp_mail($to, $subject, $message);
  }
  
  public function badge_epiry_cron_job() {
    //var_dump(__METHOD__);
    $badges = $this->get_badges();
    $badges_expires = $this->get_badges_expiry_details();
    //new dBug($badges_expires, '', 1);
    $_badgeos_achievements = $this->get_badgeos_achievements();
    //new dBug($_badgeos_achievements, '', 1);
    foreach ($_badgeos_achievements as $_badgeos_achievement) {
      //new dBug($_badgeos_achievement, '', 1);
      $user_id  = $_badgeos_achievement->user_id;
      $user     = new WP_User($user_id);
      //new dBug($user_id, '', 1);
      $badges_arr = ( ! is_array($_badgeos_achievement->meta_value)) ? unserialize($_badgeos_achievement->meta_value) : $_badgeos_achievement->meta_value;

      //new dBug($badges_arr, '', 1);
      foreach (current($badges_arr) as $badge_obj) {
        if ($badge_obj->post_type != 'badges') {
          continue;
        }
        $post_id  = $badge_obj->ID;
        //new dBug($user->display_name."({$user->ID})", '', 1);
        //new dBug($badge_obj, '', 1);
        //echo 'date_earned';
        //new dBug(gmdate('Y-m-d H:i:s', $badge_obj->date_earned));
        $validity_settings  = isset($badges_expires[$post_id]) ? $badges_expires[$post_id] : $this->get_validity_defaults();
        //new dBug($validity_settings, '', 1);
        $is_valid = $this->check_validity($user, $badge_obj, $validity_settings);
        //new dBug($is_valid, '', 1);
        if ( ! $is_valid) {
          //echo 'Expired<br />';
          badgeos_revoke_achievement_from_user($post_id, $user_id);
        }
      }
    }
  }
  
  

  public function render_achievement($output, $achievement_id) {
    global $user_ID;
    static $badges_expires;
    if ($badges_expires === null) {
      $badges_expires = $this->get_badges_expiry_details();
    }
    
    $uid  = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $user_ID;
    
    $validity_settings  = isset($badges_expires[$achievement_id]) ? $badges_expires[$achievement_id] : $this->get_validity_defaults();
    $achievements = badgeos_get_user_achievements( array( 'user_id' => $uid, 'achievement_id' => $achievement_id ) );
    if (count($achievements) == 0) return '';
    $achievement = array_pop( $achievements );
    //$achievement = badgeos_user_get_active_achievement( $user_ID, $achievement_id );

    $currentachievement = $this->calculate_expiry_date($achievement, $validity_settings);

    return '<div class="badgeos_expiry_date">Expiry Date: '.$currentachievement.'</div>'.$output;
  }
  
  public function show_expiry_date($user_content, $user_id) {
    static $validity_settings;
    
    $badge_id = get_the_ID();
    if ($validity_settings === null) {
      $badges_expires = $this->get_badges_expiry_details();
      $validity_settings  = isset($badges_expires[$badge_id]) ? $badges_expires[$badge_id] : $this->get_validity_defaults();
    }
    
    $user_has_badge = badgeos_get_user_achievements(array('user_id' => $user_id, 'achievement_id' => $badge_id,));
    //var_dump($user_has_badge);
    //die;
    
    $dom = new DOMDocument();
    $dom->loadXML($user_content);
    $lis = $dom->getElementsByTagName('li');
    foreach ($lis as $li) {
      $node = $dom->createElement('span', gmdate('d-m-Y H:i:s'));
      $node->setAttribute("class", "site-description");
      $li->appendChild($node);
    }
    return $dom->saveXML();
  }
  
  public function calculate_expiry_date($badge_obj, $validity_settings) {
    //var_dump(__METHOD__);
    static $tz;
    static $today;
    
    if (empty($tz)) {
      $tz = new DateTimeZone('GMT');
    }
    if (empty($today)) {
      $today = new DateTime('now', $tz);
    }
    $achievement_date = clone $today;
    $achievement_date = $achievement_date->setTimestamp($badge_obj->date_earned);
    
    $validity_1_field = self::prefix_field('validity_1');
    $validity_1 = (int) $validity_settings->{$validity_1_field};
    $expiry_date  = clone $achievement_date;
    $expiry_date->add(new DateInterval("P{$validity_1}D"));
    
    //return $expiry_date->format('d-m-Y H:i:s');
    return $expiry_date->format('Y-m-d');
  }

  protected function get_validity_defaults() {
    $fields = array_map(array('Badgeos_Badge_Expiry_Settings', 'prefix_field'), self::$fields);
    $obj  = new stdClass;
    foreach ($fields as $field) {
      $obj->{$field}  = '';
    }
    return $obj;
  }

  
  private static function prefix_field($field) {
    return self::FIELD_PREFIX.$field;
  }
  
  public static function activate() {
    wp_schedule_event( time(), 'daily', 'badgeos_badge_expiry_event_hook' );
  }
  
  public static function deactivate() {
    wp_clear_scheduled_hook( 'badgeos_badge_expiry_event_hook' );
    global $wpdb;
    foreach (self::$fields as $field_name) {
      $wpdb->delete( $wpdb->postmeta, array('meta_key' => self::prefix_field($field_name), ));
    }
  }
  
}
