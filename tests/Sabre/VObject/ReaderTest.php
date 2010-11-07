<?php

class Sabre_VObject_ReaderTest extends PHPUnit_Framework_TestCase {

    function testReadComponent() {

        $data = "BEGIN:VCALENDAR\r\nEND:VCALENDAR";

        $result = Sabre_VObject_Reader::read($data);

        $this->assertType('Sabre_VObject_Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    function testReadComponentUnixNewLine() {

        $data = "BEGIN:VCALENDAR\nEND:VCALENDAR";

        $result = Sabre_VObject_Reader::read($data);

        $this->assertType('Sabre_VObject_Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    function testReadComponentMacNewLine() {

        $data = "BEGIN:VCALENDAR\rEND:VCALENDAR";

        $result = Sabre_VObject_Reader::read($data);

        $this->assertType('Sabre_VObject_Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    function testReadComponentLineFold() {

        $data = "BEGIN:\r\n\tVCALENDAR\r\nE\r\n ND:VCALENDAR";

        $result = Sabre_VObject_Reader::read($data);

        $this->assertType('Sabre_VObject_Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(0, count($result->children));

    }

    /**
     * @expectedException Sabre_VObject_ParseException
     */
    function testReadCorruptComponent() {

        $data = "BEGIN:VCALENDAR\r\nEND:FOO";

        $result = Sabre_VObject_Reader::read($data);

    }

    function testReadProperty() {

        $data = "PROPNAME:propValue";
        $result = Sabre_VObject_Reader::read($data);

        $this->assertType('Sabre_VObject_Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);

    }

    /**
     * @expectedException Sabre_VObject_ParseException
     */
    function testReadBrokenLine() {

        $data = "PROPNAME;propValue";
        $result = Sabre_VObject_Reader::read($data);

    }

    function testReadPropertyInComponent() {

        $data = array(
            "BEGIN:VCALENDAR",
            "PROPNAME:propValue",
            "END:VCALENDAR"
        );

        $result = Sabre_VObject_Reader::read(implode("\r\n",$data));

        $this->assertType('Sabre_VObject_Component', $result);
        $this->assertEquals('VCALENDAR', $result->name);
        $this->assertEquals(1, count($result->children[0]));
        $this->assertType('Sabre_VObject_Property', $result->children[0]);
        $this->assertEquals('PROPNAME', $result->children[0]->name);
        $this->assertEquals('propValue', $result->children[0]->value);


    }

    function testReadPropertyAttribute() {

        $data = "PROPNAME;ATTNAME=attvalue:propValue";
        $result = Sabre_VObject_Reader::read($data);

        $this->assertType('Sabre_VObject_Property', $result);
        $this->assertEquals('PROPNAME', $result->name);
        $this->assertEquals('propValue', $result->value);
        $this->assertEquals(1, count($result->attributes));
        $this->assertEquals('ATTNAME', $result->attributes[0]->name);
        $this->assertEquals('attvalue', $result->attributes[0]->value);

    }

}