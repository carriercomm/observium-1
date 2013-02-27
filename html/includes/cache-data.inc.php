<?php

foreach (dbFetchRows("SELECT * FROM `devices`") as $device)
{

  if(device_permitted($device))
  {

    if (get_dev_attrib($device,'override_sysLocation_bool'))
    {
      $device['real_location'] = $device['location'];
      $device['location'] = get_dev_attrib($device,'override_sysLocation_string');
    }

    $devices['count']++;

    if($device['status'] == "1") { $devices['up']++; }
    if($device['status'] == "0") { $devices['down']++; }


    $cache['devices']['hostname'][$device['hostname']] = $device['device_id'];
    $cache['devices']['id'][$device['device_id']] = $device;

    $cache['device_types'][$device['type']]++;
  }
}

foreach (dbFetchRows("SELECT * FROM `ports`") as $port)
{
  if(port_permitted($port))
  {
    if($port['ifAdminStatus'] == "down") 
    {
      $ports['disabled']++;
    } else {
      if($port['ifOperStatus'] == "up") { $ports['up']++; }
      if($port['ifOperStatus'] == "down" || $port['ifOperStatus'] == "lowerLayerDown") { $ports['down']++; }
    }
  }
}



?>
