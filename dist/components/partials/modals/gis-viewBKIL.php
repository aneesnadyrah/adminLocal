
<?php 
    foreach (Tasking::taskModal() as $row) {
        if($row['StatusID'] == '004' || $row['StatusID'] == '022') {
            echo '
            <div style="display: none;">
                <div id="view-'. $row['StatusID']. $row['ID'] .'">
                    
                </div>
            </div>
            ';
        }

    }
?>