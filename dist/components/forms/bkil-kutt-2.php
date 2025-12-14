<?php
$systemId = $_GET['sid'] ?? NULL;
$details = new projectDetails();
$data = $details->getContactDetails($systemId); 

$counterpage = 0;
// $recordCount = count(ApplicationEntry::getEntry($systemId)->contacts);
$recordCount = count($data);
$sectionCount = ceil($recordCount / 4); // Calculate the number of sections needed
$recordNumber = 3; // Initialize the record number

for ($section = 0; $section < $sectionCount; $section++) {
    echo '<section class="sheet padding-10mm">';
    echo '<article>';

    $startRecord = $section * 4; // Calculate the starting record number for each section
    $endRecord = min($startRecord + 3, $recordCount - 1); // Calculate the ending record number for each section

    $firstIteration = true; // Initialize a flag
    for ($counterpage = $startRecord; $counterpage <= $endRecord; $counterpage++) {
        // $officer = ApplicationEntry::getEntry($systemId)->contacts[$counterpage];
        $officer = $data[$counterpage];

        $heading = ($section === 0 && $counterpage === $startRecord)
            ? 'Maklumat Syarikat Pemohon'
            : 'Maklumat Syarikat Penyedia Utiliti';
            
        if (!$firstIteration) {
            $head = 'Maklumat Syarikat ' . $officer->name_contact_type;
        } else {
            // If it's the first iteration, set the flag to false
            $head = 'Maklumat Syarikat Pemohon';
            $firstIteration = false;
        }

        $ofc = <<<officer
            <table>
                <thead style="background-color:lightgrey;">
                    <tr>
                        <th colspan="2">
                            <h5 style="font-weight:600; text-align: start; margin: 0.5rem">
                                $recordNumber.0 $head
                            </h5>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width:250px;">
                            <p style="margin: 0.5rem 0 0 1rem">$recordNumber.1 Nama Syarikat :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->company_name</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px;">
                            <p style="margin: 0.5rem 0 0 1rem">$recordNumber.2 Alamat :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->address_1, $officer->address_2, $officer->postcode  $officer->city, $officer->state</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:120px;">
                            <p style="margin: 0.5rem 0 0 1rem">$recordNumber.3 Nama Pegawai :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->full_name</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px;">
                            <p style="margin: 0.5rem 0 0 1rem">$recordNumber.4 Jawatan :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->position</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px;">
                            <p style="margin: 0.5rem 0 0 1rem">$recordNumber.5 Nombor Telefon Pejabat :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->phone_no</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px;">
                            <p style="margin: 0.5rem 0 0 2.3rem">Nombor Telefon Bimbit :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->phone_no</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:250px;">
                            <p style="margin: 0.5rem 0 0 1rem">$recordNumber.6 Alamat Emel :</p>
                        </td>
                        <td>
                            <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->email</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-top:1rem;"></div>
        officer;
        if (!$firstIteration) {
            echo $ofc;
        } else {
            // If it's the first iteration, set the flag to false
            $firstIteration = false;
        }
        $recordNumber++; // Increment the record number for each officer entry
    }

    echo '</article>';
    echo '</section>';
}
?>
