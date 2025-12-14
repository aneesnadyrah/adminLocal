<!-- Each sheet element should have the class "sheet" -->
<!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
<section class="sheet padding-10mm">
    <!-- Write HTML just like a web page -->
    <article>
        <?php
            $systemId = $_GET['sid'] ?? NULL; 
            $detail = new projectDetails();
            $data = $detail->getContactDetails($systemId); 

            foreach ($data as $officer) {
                $counter++; // Increment the counter for each record
                $i++;

                $recordNumber = ($counter > 1) ? $counter : "4"; // Check if it's not the first record
                if ($i < 4) {
                    $ofc = <<<officer
                    <table>
                        <thead style="background-color:lightgrey;">
                            <tr>
                                <th colspan="2">
                                    <h5 style="font-weight:600; text-align: start; margin: 0.5rem">$counter.0 Maklumat Pegawai</h5>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width:250px;">
                                    <p style="margin: 0.5rem 0 0 1rem">$counter.1 Nama Syarikat :</p>
                                </td>
                                <td>
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->name</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:250px;">
                                    <p style="margin: 0.5rem 0 0 1rem">$counter.2 Alamat :</p>
                                </td>
                                <td>
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->unit_no, $officer->street_name, $officer->postcode  $officer->city, $officer->state</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:120px;">
                                    <p style="margin: 0.5rem 0 0 1rem">$counter.3 Nama Pegawai :</p>
                                </td>
                                <td>
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->name</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:250px;">
                                    <p style="margin: 0.5rem 0 0 1rem">$counter.4 Jawatan :</p>
                                </td>
                                <td>
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->position</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:250px;">
                                    <p style="margin: 0.5rem 0 0 1rem">$counter.5 Nombor Telefon Pejabat :</p>
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
                                    <p style="margin: 0.5rem 0 0 1rem">$counter.6 Alamat Emel :</p>
                                </td>
                                <td>
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">$officer->email</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top:1rem;"></div>
                    officer;
                    echo $ofc;
                }
            }
        ?>
    </article>
</section>