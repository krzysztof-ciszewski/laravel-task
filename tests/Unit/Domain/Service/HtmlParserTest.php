<?php

namespace Tests\Unit\Domain\Service;

use App\Domain\Factory\ActivityFactoryInterface;
use App\Domain\Service\HtmlParser;
use App\Domain\ValueObject\ReportFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HtmlParserTest extends TestCase
{
    private MockObject&ActivityFactoryInterface $factory;
    private HtmlParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(ActivityFactoryInterface::class);
        $this->parser = new HtmlParser($this->factory);
    }

    #[DataProvider('parseDataProvider')]
    public function testParse(string $data): void
    {
        $activities = $this->parser->parse($data, ReportFormat::HTML);

        self::assertCount(10, $activities);
    }

    public function testParseThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->parser->parse('', ReportFormat::JSON);
    }

    public function testParseEmptyData(): void
    {
        self::assertEmpty($this->parser->parse('', ReportFormat::HTML));
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(ReportFormat $reportFormat, bool $expected): void
    {
        self::assertSame($expected, $this->parser->supports($reportFormat));
    }

    public static function supportsDataProvider(): array
    {
        return [
            [ReportFormat::HTML, true],
            [ReportFormat::JSON, false],
        ];
    }

    public static function parseDataProvider(): array
    {
        return [
            [<<<HTML
            <div class="row printOnly">
            <b>Period: 10Jan22 to 23Jan22 (ILV - Jan de Bosman)</b>
            </div>
            <table class="activityTableStyle monospace-font activityGrid_class" cellspacing="0"
                                border="0" id="ctl00_Main_activityGrid" style="border-collapse:collapse;">
                                <tbody>
                                    <tr id="ctl00_Main_activityGrid_-1" class="activity-table-header"
                                        style="font-weight:bold;">
                                        <td class="lineLeft dontPrint collapse-icon" style="width:25px;">
                                            <span id="collapseAllHeader"
                                                class="glyphicon glyphicon-minus-sign align-glyphicon"
                                                aria-hidden="true"></span>
                                        </td>
                                        <td class="lineLeft activitytablerow-date">Date</td>
                                        <td class="activitytablerow-revision visible-none-custom">Rev</td>
                                        <td class="activitytablerow-dc visible-sm-custom">DC</td>
                                        <td class="activitytablerow-checkinlt">C/I(L)</td>
                                        <td class="activitytablerow-checkinutc">C/I(Z)</td>
                                        <td class="activitytablerow-checkoutlt">C/O(L)</td>
                                        <td class="activitytablerow-checkoututc">C/O(Z)</td>
                                        <td class="activitytablerow-activity">Activity</td>
                                        <td class="activitytablerow-activityRemark">Remark</td>
                                        <td class="lineLeft lineleft1">&nbsp;</td>
                                        <td class="activitytablerow-fromstn">From</td>
                                        <td class="activitytablerow-stdlt">STD(L)</td>
                                        <td class="activitytablerow-stdutc">STD(Z)</td>
                                        <td class="lineLeft lineleft2">&nbsp;</td>
                                        <td class="activitytablerow-tostn">To</td>
                                        <td class="activitytablerow-stalt">STA(L)</td>
                                        <td class="activitytablerow-stautc">STA(Z)</td>
                                        <td class="lineLeft lineleft3">&nbsp;</td>
                                        <td class="activitytablerow-AC/Hotel">AC/Hotel</td>
                                        <td class="activitytablerow-blockhours">BLH</td>
                                        <td class="activitytablerow-flighttime visible-none-custom">
                                            <nobr>Flight Time</nobr>
                                        </td>
                                        <td class="activitytablerow-nighttime visible-none-custom">
                                            <nobr>Night Time</nobr>
                                        </td>
                                        <td class="activitytablerow-duration">Dur</td>
                                        <td class="activitytablerow-counter1">
                                            <nobr>Ext</nobr>
                                        </td>
                                        <td class="lineLeft lineleft4">&nbsp;</td>
                                        <td class="activitytablerow-Paxbooked visible-none-custom">
                                            <nobr>Pax booked</nobr>
                                        </td>
                                        <td class="activitytablerow-Tailnumber">ACReg</td>
                                        <td class="activitytablerow-CrewMeal">CrewMeal</td>
                                        <td class="lineLeft lineleft5">&nbsp;</td>
                                        <td class="activitytablerow-Resources visible-none-custom">Resources</td>
                                        <td class="activitytablerow-crewcodelist">CC</td>
                                        <td class="activitytablerow-fullnamelist visible-none-custom">Name</td>
                                        <td class="activitytablerow-positionlist">Pos.</td>
                                        <td class="activitytablerow-BusinessPhoneList visible-none-custom">
                                            <nobr>Work Phone</nobr>
                                        </td>
                                        <td class="activitytablerow-OtherDHCrewCode">
                                            <nobr>DH Crew</nobr>
                                        </td>
                                        <td class="activitytablerow-DHFullNameList visible-none-custom">
                                            <nobr>DH Name</nobr>
                                        </td>
                                        <td class="activitytablerow-DHSeatingList visible-none-custom">
                                            <nobr>DH Seat</nobr>
                                        </td>
                                        <td class="activitytablerow-remarks">Remarks</td>
                                        <td class="activitytablerow-ActualFdpTime">
                                            <nobr>Fdp Time</nobr>
                                        </td>
                                        <td class="activitytablerow-MaxFdpTime">
                                            <nobr>Max Fdp</nobr>
                                        </td>
                                        <td class="activitytablerow-RestCompletedTime visible-none-custom">
                                            <nobr>Rest Compl.</nobr>
                                        </td>
                                        <td class="lineRight lineright1">&nbsp;</td>
                                    </tr>
                                    <tr id="ctl00_Main_activityGrid_0" class="lineTop">
                                        <td class="lineLeft dontPrint expand-icon">
                                            <span class="glyphicon glyphicon-plus-sign align-glyphicon"></span>
                                        </td>
                                        <td class="lineLeft activitytablerow-date">
                                            <nobr>Mon 10</nobr>
                                        </td>
                                        <td class="activitytablerow-revision visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-dc visible-sm-custom">&nbsp;</td>
                                        <td class="activitytablerow-checkinlt">0845</td>
                                        <td class="activitytablerow-checkinutc">0745</td>
                                        <td class="activitytablerow-checkoutlt">&nbsp;</td>
                                        <td class="activitytablerow-checkoututc">&nbsp;</td>
                                        <td class="activitytablerow-activity">DX77</td>
                                        <td class="activitytablerow-activityRemark">DX 0077</td>
                                        <td class="lineLeft lineleft1">&nbsp;</td>
                                        <td class="activitytablerow-fromstn">KRP</td>
                                        <td class="activitytablerow-stdlt">0945</td>
                                        <td class="activitytablerow-stdutc">0845</td>
                                        <td class="lineLeft lineleft2">&nbsp;</td>
                                        <td class="activitytablerow-tostn">CPH</td>
                                        <td class="activitytablerow-stalt">1035</td>
                                        <td class="activitytablerow-stautc">0935</td>
                                        <td class="lineLeft lineleft3">&nbsp;</td>
                                        <td class="activitytablerow-AC/Hotel">
                                            DO4
                                        </td>
                                        <td class="activitytablerow-blockhours">&nbsp;</td>
                                        <td class="activitytablerow-flighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-nighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-duration">&nbsp;</td>
                                        <td class="activitytablerow-counter1">&nbsp;</td>
                                        <td class="lineLeft lineleft4">&nbsp;</td>
                                        <td class="activitytablerow-Paxbooked visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-Tailnumber">OYJRY</td>
                                        <td class="activitytablerow-CrewMeal">&nbsp;</td>
                                        <td class="lineLeft lineleft5">&nbsp;</td>
                                        <td class="activitytablerow-Resources visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-crewcodelist">
                                            <nobr>JBN</nobr><br>
                                            <nobr>THI</nobr><br>
                                            <nobr>ILV</nobr>
                                        </td>
                                        <td class="activitytablerow-fullnamelist visible-none-custom">

                                        </td>
                                        <td class="activitytablerow-positionlist">
                                            <nobr>CP (PIC)</nobr><br>
                                            <nobr>FO</nobr><br>
                                            <nobr>CA</nobr>
                                        </td>
                                        <td class="activitytablerow-BusinessPhoneList visible-none-custom">

                                        </td>
                                        <td class="activitytablerow-OtherDHCrewCode">

                                        </td>
                                        <td class="activitytablerow-DHFullNameList visible-none-custom">

                                        </td>
                                        <td class="activitytablerow-DHSeatingList visible-none-custom">
                                            <br><br>
                                        </td>
                                        <td class="activitytablerow-remarks">&nbsp;</td>
                                        <td class="activitytablerow-ActualFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-MaxFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-RestCompletedTime visible-none-custom">&nbsp;</td>
                                        <td class="lineRight lineright1">&nbsp;</td>
                                    </tr>
                                    <tr id="ctl00_Main_activityGrid_3">
                                        <td class="lineLeft dontPrint expand-icon">
                                            <span class="glyphicon glyphicon-plus-sign align-glyphicon"></span>
                                        </td>
                                        <td class="lineLeft activitytablerow-date"></td>
                                        <td class="activitytablerow-revision visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-dc visible-sm-custom">&nbsp;</td>
                                        <td class="activitytablerow-checkinlt">&nbsp;</td>
                                        <td class="activitytablerow-checkinutc">&nbsp;</td>
                                        <td class="activitytablerow-checkoutlt">1855</td>
                                        <td class="activitytablerow-checkoututc">1755</td>
                                        <td class="activitytablerow-activity">DX82</td>
                                        <td class="activitytablerow-activityRemark">DX 0082</td>
                                        <td class="lineLeft lineleft1">&nbsp;</td>
                                        <td class="activitytablerow-fromstn">CPH</td>
                                        <td class="activitytablerow-stdlt">1745</td>
                                        <td class="activitytablerow-stdutc">1645</td>
                                        <td class="lineLeft lineleft2">&nbsp;</td>
                                        <td class="activitytablerow-tostn">KRP</td>
                                        <td class="activitytablerow-stalt">1835</td>
                                        <td class="activitytablerow-stautc">1735</td>
                                        <td class="lineLeft lineleft3">&nbsp;</td>
                                        <td class="activitytablerow-AC/Hotel">
                                            DO4
                                        </td>
                                        <td class="activitytablerow-blockhours">3:20</td>
                                        <td class="activitytablerow-flighttime visible-none-custom">0:50</td>
                                        <td class="activitytablerow-nighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-duration">10:10</td>
                                        <td class="activitytablerow-counter1">0</td>
                                        <td class="lineLeft lineleft4">&nbsp;</td>
                                        <td class="activitytablerow-Paxbooked visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-Tailnumber">OYJRY</td>
                                        <td class="activitytablerow-CrewMeal">&nbsp;</td>
                                        <td class="lineLeft lineleft5">&nbsp;</td>
                                        <td class="activitytablerow-Resources visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-crewcodelist">|</td>
                                        <td class="activitytablerow-fullnamelist visible-none-custom">|</td>
                                        <td class="activitytablerow-positionlist">|</td>
                                        <td class="activitytablerow-BusinessPhoneList visible-none-custom">

                                        </td>
                                        <td class="activitytablerow-OtherDHCrewCode">
                                            <nobr>VIO</nobr>
                                        </td>
                                        <td class="activitytablerow-DHFullNameList visible-none-custom"></td>
                                        <td class="activitytablerow-DHSeatingList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-remarks">&nbsp;</td>
                                        <td class="activitytablerow-ActualFdpTime">9:50</td>
                                        <td class="activitytablerow-MaxFdpTime">12:00</td>
                                        <td class="activitytablerow-RestCompletedTime visible-none-custom">0655+1</td>
                                        <td class="lineRight lineright1">&nbsp;</td>
                                    </tr>
                                    <tr id="ctl00_Main_activityGrid_8" class="lineTop">
                                        <td class="lineLeft dontPrint expand-icon">
                                            <span class="glyphicon glyphicon-plus-sign align-glyphicon"></span>
                                        </td>
                                        <td class="lineLeft activitytablerow-date">
                                            <nobr>Wed 12</nobr>
                                        </td>
                                        <td class="activitytablerow-revision visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-dc visible-sm-custom">&nbsp;</td>
                                        <td class="activitytablerow-checkinlt">&nbsp;</td>
                                        <td class="activitytablerow-checkinutc">&nbsp;</td>
                                        <td class="activitytablerow-checkoutlt">&nbsp;</td>
                                        <td class="activitytablerow-checkoututc">&nbsp;</td>
                                        <td class="activitytablerow-activity">OFF</td>
                                        <td class="activitytablerow-activityRemark">OFF</td>
                                        <td class="lineLeft lineleft1">&nbsp;</td>
                                        <td class="activitytablerow-fromstn">KRP</td>
                                        <td class="activitytablerow-stdlt">0000</td>
                                        <td class="activitytablerow-stdutc">2300-1</td>
                                        <td class="lineLeft lineleft2">&nbsp;</td>
                                        <td class="activitytablerow-tostn">KRP</td>
                                        <td class="activitytablerow-stalt">2400</td>
                                        <td class="activitytablerow-stautc">2300</td>
                                        <td class="lineLeft lineleft3">&nbsp;</td>
                                        <td class="activitytablerow-AC/Hotel">
                                        </td>
                                        <td class="activitytablerow-blockhours">&nbsp;</td>
                                        <td class="activitytablerow-flighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-nighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-duration">&nbsp;</td>
                                        <td class="activitytablerow-counter1">&nbsp;</td>
                                        <td class="lineLeft lineleft4">&nbsp;</td>
                                        <td class="activitytablerow-Paxbooked visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-Tailnumber">&nbsp;</td>
                                        <td class="activitytablerow-CrewMeal">&nbsp;</td>
                                        <td class="lineLeft lineleft5">&nbsp;</td>
                                        <td class="activitytablerow-Resources visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-crewcodelist">
                                            <nobr>ILV</nobr>
                                        </td>
                                        <td class="activitytablerow-fullnamelist visible-none-custom"></td>
                                        <td class="activitytablerow-positionlist">-</td>
                                        <td class="activitytablerow-BusinessPhoneList visible-none-custom">

                                        </td>
                                        <td class="activitytablerow-OtherDHCrewCode">&nbsp;</td>
                                        <td class="activitytablerow-DHFullNameList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-DHSeatingList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-remarks">&nbsp;</td>
                                        <td class="activitytablerow-ActualFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-MaxFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-RestCompletedTime visible-none-custom">&nbsp;</td>
                                        <td class="lineRight lineright1">&nbsp;</td>
                                    </tr>
                                    
                                    <tr id="ctl00_Main_activityGrid_11">
                                        <td class="lineLeft dontPrint expand-icon">
                                            <span class="glyphicon glyphicon-plus-sign align-glyphicon"></span>
                                        </td>
                                        <td class="lineLeft activitytablerow-date">
                                            <nobr>Sat 15</nobr>
                                        </td>
                                        <td class="activitytablerow-revision visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-dc visible-sm-custom">&nbsp;</td>
                                        <td class="activitytablerow-checkinlt">0600</td>
                                        <td class="activitytablerow-checkinutc">0500</td>
                                        <td class="activitytablerow-checkoutlt">1800</td>
                                        <td class="activitytablerow-checkoututc">1700</td>
                                        <td class="activitytablerow-activity">SBY</td>
                                        <td class="activitytablerow-activityRemark">SBY</td>
                                        <td class="lineLeft lineleft1">&nbsp;</td>
                                        <td class="activitytablerow-fromstn">KRP</td>
                                        <td class="activitytablerow-stdlt">0600</td>
                                        <td class="activitytablerow-stdutc">0500</td>
                                        <td class="lineLeft lineleft2">&nbsp;</td>
                                        <td class="activitytablerow-tostn">KRP</td>
                                        <td class="activitytablerow-stalt">1800</td>
                                        <td class="activitytablerow-stautc">1700</td>
                                        <td class="lineLeft lineleft3">&nbsp;</td>
                                        <td class="activitytablerow-AC/Hotel">
                                        </td>
                                        <td class="activitytablerow-blockhours">0:00</td>
                                        <td class="activitytablerow-flighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-nighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-duration">3:00</td>
                                        <td class="activitytablerow-counter1">0</td>
                                        <td class="lineLeft lineleft4">&nbsp;</td>
                                        <td class="activitytablerow-Paxbooked visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-Tailnumber">&nbsp;</td>
                                        <td class="activitytablerow-CrewMeal">&nbsp;</td>
                                        <td class="lineLeft lineleft5">&nbsp;</td>
                                        <td class="activitytablerow-Resources visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-crewcodelist">|</td>
                                        <td class="activitytablerow-fullnamelist visible-none-custom">|</td>
                                        <td class="activitytablerow-positionlist">|</td>
                                        <td class="activitytablerow-BusinessPhoneList visible-none-custom">

                                        </td>
                                        <td class="activitytablerow-OtherDHCrewCode">&nbsp;</td>
                                        <td class="activitytablerow-DHFullNameList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-DHSeatingList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-remarks">&nbsp;</td>
                                        <td class="activitytablerow-ActualFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-MaxFdpTime">4:00</td>
                                        <td class="activitytablerow-RestCompletedTime visible-none-custom">0600+1</td>
                                        <td class="lineRight lineright1">&nbsp;</td>
                                    </tr>
                                    <tr id="ctl00_Main_activityGrid_27" class="lineTop">
                                        <td class="lineLeft dontPrint expand-icon">
                                            <span class="glyphicon glyphicon-plus-sign align-glyphicon"></span>
                                        </td>
                                        <td class="lineLeft activitytablerow-date">
                                            <nobr>Sat 22</nobr>
                                        </td>
                                        <td class="activitytablerow-revision visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-dc visible-sm-custom">DH</td>
                                        <td class="activitytablerow-checkinlt">0655</td>
                                        <td class="activitytablerow-checkinutc">0555</td>
                                        <td class="activitytablerow-checkoutlt">&nbsp;</td>
                                        <td class="activitytablerow-checkoututc">&nbsp;</td>
                                        <td class="activitytablerow-activity">CAR</td>
                                        <td class="activitytablerow-activityRemark">Comapany Car</td>
                                        <td class="lineLeft lineleft1">&nbsp;</td>
                                        <td class="activitytablerow-fromstn">KRP</td>
                                        <td class="activitytablerow-stdlt">0655</td>
                                        <td class="activitytablerow-stdutc">0555</td>
                                        <td class="lineLeft lineleft2">&nbsp;</td>
                                        <td class="activitytablerow-tostn">EBJ</td>
                                        <td class="activitytablerow-stalt">0820</td>
                                        <td class="activitytablerow-stautc">0720</td>
                                        <td class="lineLeft lineleft3">&nbsp;</td>
                                        <td class="activitytablerow-AC/Hotel">
                                        </td>
                                        <td class="activitytablerow-blockhours">&nbsp;</td>
                                        <td class="activitytablerow-flighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-nighttime visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-duration">&nbsp;</td>
                                        <td class="activitytablerow-counter1">&nbsp;</td>
                                        <td class="lineLeft lineleft4">&nbsp;</td>
                                        <td class="activitytablerow-Paxbooked visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-Tailnumber">&nbsp;</td>
                                        <td class="activitytablerow-CrewMeal">&nbsp;</td>
                                        <td class="lineLeft lineleft5">&nbsp;</td>
                                        <td class="activitytablerow-Resources visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-crewcodelist">&nbsp;</td>
                                        <td class="activitytablerow-fullnamelist visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-positionlist">&nbsp;</td>
                                        <td class="activitytablerow-BusinessPhoneList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-OtherDHCrewCode">&nbsp;</td>
                                        <td class="activitytablerow-DHFullNameList visible-none-custom"></td>
                                        <td class="activitytablerow-DHSeatingList visible-none-custom">&nbsp;</td>
                                        <td class="activitytablerow-remarks">&nbsp;</td>
                                        <td class="activitytablerow-ActualFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-MaxFdpTime">&nbsp;</td>
                                        <td class="activitytablerow-RestCompletedTime visible-none-custom">&nbsp;</td>
                                        <td class="lineRight lineright1">&nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>
HTML]
        ];
    }
}
