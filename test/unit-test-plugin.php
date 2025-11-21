<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'disciple-tools-people-groups-api/disciple-tools-people-groups-api.php' );

        $this->assertContains(
            'disciple-tools-people-groups-api/disciple-tools-people-groups-api.php',
            get_option( 'active_plugins' )
        );
    }
}
