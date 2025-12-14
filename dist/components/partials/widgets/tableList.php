<?php 
    $processedBalances = array();

    echo <<<TEMPLATE
        <div class="card card-stretch card-flush mb-5">
            <!--begin::Header-->
            <div class="card-header pt-7">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">			
                    <span class="card-label fw-bold text-gray-800">$data->title</span>
                    <span id="counter_$data->table_id" class="text-gray-500 mt-1 fw-semibold fs-6"></span>
                </h3>
                <!--end::Title-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-6">             
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table id="{$data->table_id}" class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fs-7 fw-bold text-uppercase text-gray-500 border-bottom-0">             
        TEMPLATE;
                            foreach ($data->headers as $header) {
                                echo <<<TEMPLATE
                                                    <th class="pb-3 w-auto">$header</th>
                                                TEMPLATE;
                            }
                            echo <<<TEMPLATE
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                        TEMPLATE;
                        // Display records
                        foreach ($data->records as $record) {
                            if (!in_array($record->balance, $processedBalances)) {
                                echo "<tr>";
                                // Display body data for each record
                                foreach ($data->body as $body) {
                                    if ($body === "reference_no") {
                                        $sub = $record->invoice_no ? $record->invoice_no : $record->system_id;
                                        echo <<<TEMPLATE
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-50px me-3">                                                   
                                                    <img src="{$record->provider->logo}" class="" alt="{$record->provider->name}" />                                                    
                                                </div>
                                                
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="/projects/details/{$record->system_id}" class="text-gray-800 fw-bold text-hover-primary mb-1 fs-6">{$record->$body}</a>
                                                    <span class="text-gray-500 fw-semibold d-block fs-7">$sub</span>
                                                </div>
                                            </div>
                                        </td>
                                        TEMPLATE;
                                    } 
                                    elseif ($body === "created_at" || $body === "updated_at" || $body === "submitted_date" || $body === "created_date") {
                                        $date = new DateTime($record->$body);
                                        echo '<td>'. $date->format('d/m/Y') .'</td>';
                                    }
                                    elseif ($body === "status") {
                                        echo <<<TEMPLATE
                                        <td>
                                            <span class="badge badge-light-{$record->status_color} me-2">{$record->$body}</span>
                                        </td>
                                        TEMPLATE;
                                    }
                                    elseif ($body === "districts") {
                                        $districts = explode(",", $record->$body);
                                        echo '<td>';
                                        foreach ($districts as $district) {
                                            echo '<span class="badge badge-secondary my-1 me-2">'. $district .'</span>';
                                        } 
                                        echo '</td>';

                                    }
                                    elseif ($body === 'balance' || $body === 'invoice_amount') {
                                        $body === 'balance' ? $color = 'danger' : $color = 'success';
                                        $body === 'balance' ? $symbol = '-' : $symbol = '+';
                                        echo '<td class="fw-bold text-'.$color.'">'. $symbol .' RM '. number_format($record->$body) .'</td>';
                                    }
                                    elseif (isset($record->$body)) {
                                        echo "<td>{$record->$body}</td>";
                                    } else {
                                        echo "<td> - </td>";
                                    }
                                }
                            }
                            echo "</tr>";
                            // Mark the balance as processed
                            $processedBalances[] = $record->balance;
                        }
                        echo <<<TEMPLATE
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    
                </div>
                <!--end::Table-->
            </div>
            <div id="paginate" class="pagination pagination-outline mb-7"></div>
            <!--end: Card Body-->
        </div>
        <script>
        var table=document.querySelector("#$data->table_id"),counter=document.getElementById("counter_$data->table_id");document.addEventListener("DOMContentLoaded",function(){var e=new DataTable(table,{info:!1,lengthChange:!1,language:{loadingRecords:"Sila Tunggu...",zeroRecords:"Tiada Rekod Dijumpai"},pageLength:5,lengthMenu:[[5,10,25,50,-1],[5,10,25,50,"Semua"]],order:[[1,"desc"]]});counter.innerText=e.rows().count()+" Permohonan",$("#paginate").append($(".dataTables_paginate"))});
        </script>
    TEMPLATE;

