<?php

// Set Defaults here

if(!isset($vars['format'])) { $vars['format'] = "detail"; }
if (!$config['web_show_disabled'] && !isset($vars['disabled'])) { $vars['disabled'] = '0'; }

/// FIXME - new style of searching here

$sql_param = array();
$where = ' WHERE 1 ';
foreach ($vars as $var => $value)
{
  if ($value != '')
  {
    switch ($var)
    {
      case 'hostname':
        $where .= ' AND `hostname` LIKE ?';
        $sql_param[] = '%'.$value.'%';
        break;
      case 'sysname':
        $where .= ' AND `sysName` LIKE ?';
        $sql_param[] = '%'.$value.'%';
        break;
      case 'location_text':
        $where .= ' AND `location` LIKE ?';
        $sql_param[] = '%'.str_replace('*', '%', $value).'%';
        break;
      case 'os':
      case 'version':
      case 'hardware':
      case 'features':
      case 'type':
      case 'status':
      case 'ignore':
      case 'disabled':
      case 'location_country':
      case 'location_state':
      case 'location_county':
      case 'location_city':
      case 'location':
        $where .= ' AND `'.$var.'` = ?';
        $sql_param[] = $value;
        break;
    }
  }
}

$pagetitle[] = "Devices";

echo('<div class="well" style="padding: 10px;">');

if($vars['searchbar'] != "hide")
{

?>

<form method="post" class="form form-inline" action="" id="devices-form" style="margin-bottom:0;">

<?php

  // Loop variables which can be set by button to make sure they're squeezed in to the URL.

  foreach(array('bare', 'searchbar', 'format') as $element) {
    if(isset($vars[$element])) {
      echo '<input type="hidden" name="',$element,'" value="',$vars[$element],'" />';
    }
  }

?>

    <div class="row">
      <div class="col-lg-2">
          <input placeholder="Hostname" type="text" name="hostname" id="hostname" class="input" value="<?php htmlentities($vars['hostname']); ?>" />
      </div>
      <div class="col-lg-2">
        <select class="selectpicker" name="location" id="location">
          <option value="">All Locations</option>
          <?php
// fix me function?

foreach (getlocations() as $location) // FIXME function name sucks maybe get_locations ?
{
  if ($location)
  {
    echo('<option value="'.$location.'"');
    if ($location == $vars['location']) { echo(" selected"); }
    echo(">".$location."</option>");
  }
}
?>
        </select>
      </div>
      <div class="col-lg-2">
        <select class="selectpicker" name='os' id='os'>
          <option value=''>All OSes</option>
          <?php

$where_form = ($config['web_show_disabled']) ? '' : 'AND disabled = 0';
foreach (dbFetch('SELECT `os` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `os` ORDER BY `os`') as $data)
{
  if ($data['os'])
  {
    echo("<option value='".$data['os']."'");
    if ($data['os'] == $vars['os']) { echo(" selected"); }
    echo(">".$config['os'][$data['os']]['text']."</option>");
  }
}
          ?>
        </select>
      </div>
      <div class="col-lg-2">
        <select class="selectpicker" name="hardware" id="hardware">
          <option value="">All Platforms</option>
          <?php
foreach (dbFetch('SELECT `hardware` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `hardware` ORDER BY `hardware`') as $data)
{
  if ($data['hardware'])
  {
    echo('<option value="'.$data['hardware'].'"');
    if ($data['hardware'] == $vars['hardware']) { echo(" selected"); }
    echo(">".$data['hardware']."</option>");
  }
}
          ?>
        </select>
      </div>
    </div>
    <div class="row" style="margin-top: 10px;">
      <div class="col-lg-2">
          <input placeholder="sysName" type="text" name="sysname" id="sysname" class="input" value="<?php echo($vars['sysname']); ?>" />
      </div>
      <div class="col-lg-2">
          <input placeholder="Location" type="text" name="location_text" id="location_text" class="input" value="<?php echo($vars['location_text']); ?>" />
      </div>
      <div class="col-lg-2">
        <select  class="selectpicker" name='version' id='version'>
          <option value=''>All Versions</option>
          <?php

foreach (dbFetch('SELECT `version` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `version` ORDER BY `version`') as $data)
{
  if ($data['version'])
  {
    echo("<option value='".$data['version']."'");
    if ($data['version'] == $vars['version']) { echo(" selected"); }
    echo(">".$data['version']."</option>");
  }
}
          ?>
        </select>
      </div>
      <div class="col-lg-2">
        <select class="selectpicker" name="features" id="features">
          <option value="">All Featuresets</option>
          <?php

foreach (dbFetch('SELECT `features` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `features` ORDER BY `features`') as $data)
{
  if ($data['features'])
  {
    echo('<option value="'.$data['features'].'"');
    if ($data['features'] == $vars['features']) { echo(" selected"); }
    echo(">".$data['features']."</option>");
  }
}
          ?>
        </select>

      </div>
      <div class="col-lg-2">
        <select class="selectpicker" name="type" id="type">
          <option value="">All Device Types</option>
          <?php

foreach (dbFetch('SELECT `type` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `type` ORDER BY `type`') as $data)
{
  if ($data['type'])
  {
    echo("<option value='".$data['type']."'");
    if ($data['type'] == $vars['type']) { echo(" selected"); }
    echo(">".ucfirst($data['type'])."</option>");
  }
}
          ?>
        </select>

      </div>
      <div class="col-lg-2 pull-right">
        <button type="submit" onClick="submitURL();" class="btn pull-right"><i class="icon-search"></i> Search</button>
      </div>

    </div>
  
</form>

<script>

// This code updates the FORM URL

function submitURL() {
  var url = '/devices/';

    var partFields = document.getElementById("devices-form").elements;

    for(var el, i = 0, n = partFields.length; i < n; i++) {
      el = partFields[i];
      if(el.value != '') {
        if (el.checked || el.type !== "checkbox") {
            url += encodeURIComponent(el.name) + "=" +
                   encodeURIComponent(el.value) + "/"
            ;
        }
      }
    }

   $('#devices-form').attr('action', url);

}

</script>

</div>

<?php

}

$navbar['brand'] = "Devices";

$navbar = array('brand' => "Ports", 'class' => "navbar-narrow");

$navbar['options']['basic']['text']    = 'Basic';
$navbar['options']['detail']['text']   = 'Details';
$navbar['options']['status']['text']   = 'Status';

// There is no detailed view for this yet.
//$navbar['options']['detail']['text']  = 'Details';

$navbar['options']['graphs']     = array('text' => 'Graphs');

foreach ($navbar['options'] as $option => $array)
{
  if (!isset($vars['format'])) { $vars['format'] = 'basic'; }
  if ($vars['format'] == $option) { $navbar['options'][$option]['class'] .= " active"; }
  $navbar['options'][$option]['url'] = generate_url($vars,array('format' => $option));
}

$menu_options = array('bits'      => 'Bits',
                      'processor' => 'CPU',
                      'mempool'   => 'Memory',
                      'uptime'    => 'Uptime',
                      'storage'   => 'Storage',
                      'diskio'    => 'Disk I/O',
                      'poller_perf' => 'Poll Time'
                      );

foreach (array('graphs') as $type)
{
  foreach ($config['graph_types']['device'] as $option => $data)
  {
    if(!isset($data['name'])) { $data['name'] = nicecase($option);}


    if ($vars['format'] == $type && $vars['graph'] == $option)
    {
      $navbar['options'][$type]['suboptions'][$option]['class'] = 'active';
      $navbar['options'][$type]['text'] .= " (".$data['name'].')';
    }
    $navbar['options'][$type]['suboptions'][$option]['text'] = $data['name'];
    $navbar['options'][$type]['suboptions'][$option]['url'] = generate_url($vars, array('view' => NULL, 'format' => $type, 'graph' => $option));
  }
}

  if ($vars['searchbar'] == "hide")
  {
    $navbar['options_right']['searchbar']     = array('text' => 'Show Search', 'url' => generate_url($vars, array('searchbar' => NULL)));
  } else {
    $navbar['options_right']['searchbar']     = array('text' => 'Hide Search' , 'url' => generate_url($vars, array('searchbar' => 'hide')));
  }

  if ($vars['bare'] == "yes")
  {
    $navbar['options_right']['header']     = array('text' => 'Show Header', 'url' => generate_url($vars, array('bare' => NULL)));
  } else {
    $navbar['options_right']['header']     = array('text' => 'Hide Header', 'url' => generate_url($vars, array('bare' => 'yes')));
  }

  $navbar['options_right']['reset']        = array('text' => 'Reset', 'url' => generate_url(array('page' => 'devices', 'section' => $vars['section'], 'bare' => $vars['bare'])));

print_navbar($navbar);
unset($navbar);

$query = "SELECT * FROM `devices` " . $where . " ORDER BY hostname";

list($format, $subformat) = explode("_", $vars['format'], 2);

$devices = dbFetchRows($query, $sql_param);

if(count($devices))
{
  if (file_exists('pages/devices/'.$format.'.inc.php'))
  {
    include('pages/devices/'.$format.'.inc.php');
  } else {
?>

<div class="alert alert-error">
  <h4>Error</h4>
  This should not happen. Please ensure you are on the latest release and then report this to the Observium developers if it continues.
</div>

<?php
  }

} else {

?>
<div class="alert alert-error">
  <h4>No devices found</h4>
  Please try adjusting your search parameters.
</div>

<?php
}
