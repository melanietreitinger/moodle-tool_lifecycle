<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/generator/lib.php');
require_once(__DIR__ . '/../lib.php');

use tool_cleanupcourses\manager\workflow_manager;
use tool_cleanupcourses\entity\workflow;

/**
 * Tests the field is manual after activating workflows.
 * @package    tool_cleanupcourses
 * @category   test
 * @group      tool_cleanupcourses
 * @copyright  2018 WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_cleanupcourses_workflow_is_manual_testcase extends \advanced_testcase {
    const MANUAL_TRIGGER1_ICON = 't/up';
    const MANUAL_TRIGGER1_DISPLAYNAME = 'Up';
    const MANUAL_TRIGGER1_CAPABILITY = 'moodle/course:manageactivities';

    private $manualworkflow;
    private $automaticworkflow;

    public function setUp() {
        $this->resetAfterTest(true);

        $settings = new stdClass();
        $settings->icon = self::MANUAL_TRIGGER1_ICON;
        $settings->displayname = self::MANUAL_TRIGGER1_DISPLAYNAME;
        $settings->capability = self::MANUAL_TRIGGER1_CAPABILITY;
        $this->manualworkflow = tool_cleanupcourses_generator::create_manual_workflow($settings);
        $this->automaticworkflow = tool_cleanupcourses_generator::create_workflow();

        $this->assertNull($this->manualworkflow->manual);
        $this->assertNull($this->automaticworkflow->manual);
    }

    /**
     * Test to activate the manual workflow.
     */
    public function test_activate_manual() {
        workflow_manager::handle_action(ACTION_WORKFLOW_ACTIVATE, $this->manualworkflow->id);
        $reloadworkflow = workflow_manager::get_workflow($this->manualworkflow->id);
        $this->assertTrue(workflow_manager::is_active($this->manualworkflow->id));
        $this->assertTrue($reloadworkflow->manual);
    }

    /**
     * Test to activate the automatic workflow.
     */
    public function test_activate_automatic() {
        workflow_manager::handle_action(ACTION_WORKFLOW_ACTIVATE, $this->automaticworkflow->id);
        $reloadworkflow = workflow_manager::get_workflow($this->automaticworkflow->id);
        $this->assertTrue(workflow_manager::is_active($this->automaticworkflow->id));
        $this->assertEquals(false, $reloadworkflow->manual);
    }
}