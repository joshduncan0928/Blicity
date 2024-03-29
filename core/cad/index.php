<?php

/**
Blicity CAD/MDT
Copyright (C) 2018 Decyphr and Blicity.
 Credit is not allowed to be removed from this program, doing so will
 result in copyright takedown.
 WE DO NOT SUPPORT CHANGING CODE IN ANYWAY, AS IT WILL MESS WITH FUTURE
 UPDATES. NO SUPPORT IS PROVIDED FOR CODE THAT IS EDITED.
**/

$file_access = "11111111";
require '../../core/includes/check_access.php';
require_once '../../core/includes/cdn_settings.php';

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    $connection = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    $result = $connection->query("SELECT * FROM units WHERE uuid='$query'");
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if ($row['association'] != $uuid) {
                header("Location: ../index.php?noAccess");
            } else {
                if ($row['dispatch'] == 0) {
                    header("Location: ../index.php?noAccess");
                }
            }
        }
    } else {
        header("Location: ../index.php?noAccess");
    }
} else {
    header("Location: ../index.php?noAccess");
}
if (session_id() == '' || !isset($_SESSION)) {
    session_start();
}
$_SESSION['identifier'] = $_GET['q'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo TITLE; ?> | CAD</title>
        <?php
        foreach ($requiredFiles as $file) {
            echo $file;
        }
        echo SOLID;
        echo FONTAWESOME;
        echo BOOTSTRAP_NUMBER_INPUT;
        echo SELECT2;
        echo SELECT2_REMOTECSS;
        echo SELECT2_CSS;
        echo DISPATCH_JS;
        ?>
    <style>
        .table th , .table td {
            padding: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>
                    CAD Tools
                    <a href="#" class="sidebarCollapse">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </h3>
                <strong>
                    <a href="#" class="sidebarCollapse">
                        BC&#9776;
                    </a>
                </strong>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="../">
                        <i class="fas fa-home"></i>
                        Go Home
                    </a>
                    <a data-toggle="modal" data-target="#addBoloModal">
                        <i class="fas fa-car"></i>
                        Add Bolo
                    </a>
                    <a data-toggle="modal" data-target="#callModal">
                        <i class="fas fa-car"></i>
                        Add Call
                    </a>
                    <a data-toggle="modal" data-target="#notesModal">
                        <i class="fas fa-drivers-license"></i>
                        Note Pad
                    </a>
                </li>
            </ul>
        </nav>

        <div class="modal fade" id="callModal" role="dialog" aria-labelledby="callModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="max-height:90vh;overflow-y:auto;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="callModalLabel">New Call</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div id="callForm">
                                <div class="form-group">
                                    <label for="callDescText">Call Description</label>
                                    <input type="text" class="form-control" id="callDescText" placeholder="Call Description">
                                    <small id="callDescTextHelp" class="form-text" style="color:red; display:none;">Cannot be left blank.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-bottom:10px;">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="createCall();">Create Call</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="notesModal" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notesModalLabel">Note Pad</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea id="notesTA" style="width:100%; height: 300px;background-color: #fcff82; border: none;"><?php
                        $connection2 = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
                        if ($connection->connect_error) {
                            die("Connection failed: " . $connection->connect_error);
                        }
                        $result2 = $connection2->query("SELECT notes FROM units WHERE uuid='$query'");
                        if ($result2->num_rows > 0) {
                            while($row = $result2->fetch_assoc()) {
                                echo $row['notes'];
                            }
                        }
                        ?></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" onclick="saveNotes();">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addBoloModal" role="dialog" aria-labelledby="addBoloModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Bolo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="vehMakeModelText">Vehicle Make/Model</label>
                            <input type="text" class="form-control" id="vehMakeModelText" placeholder="Make/Model">
                            <small id="vehMakeModelHelp" class="form-text" style="display:none; color:red;">Cannot leave empty.</small>
                        </div>
                        <div class="form-group">
                            <label for="vehColorText">Vehicle Color</label>
                            <input type="text" class="form-control" id="vehColorText" placeholder="Color">
                            <small id="vehColorHelp" class="form-text" style="display:none; color:red;">Cannot leave empty.</small>
                        </div>
                        <div class="form-group">
                            <label for="lpText">Vehicle License Plate</label>
                            <input type="text" class="form-control" id="lpText" placeholder="License Plate">
                            <small id="lpHelp" class="form-text text-muted">Leave blank if unknown.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="addBoloBtn">Add bolo</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="charLookupModal" role="dialog" aria-labelledby="charLookupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xlg" role="document">
                <div class="modal-content" style="max-height:90vh;overflow-y:auto;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Character Lookup</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="characterSelect">Character Lookup</label>
                            <select class="js-example-basic-single" name="state" style="" onchange="showChar(this.value)" id="testsel">
                                <?php
                                $connection = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
                                if ($connection->connect_error) {
                                    die("Connection failed: " . $connection->connect_error);
                                }
                                echo '<option selected data-select2-id="' . mysqli_num_rows($result) . '">Name or Address</option>';
                                $result = $connection->query("SELECT * FROM characters");
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['uuid'] . '">' . $row['name'] . ' - ' . $row['age'] . ' - ' . $row['address'] . '</option>';
                                    }
                                }
                                ?>
                            </select>

                            <div id="showChar"></div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-bottom:10px;">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ticketModal" role="dialog" aria-labelledby="charLookupModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="max-height:90vh;overflow-y:auto;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Issue Ticket</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="characterSelect">Issue Ticket To</label>
                            <select class="js-example-basic-single" name="state" style="" id="testsel2">
                                <?php
                                $connection = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
                                if ($connection->connect_error) {
                                    die("Connection failed: " . $connection->connect_error);
                                }
                                echo '<option value="default" selected data-select2-id="' . mysqli_num_rows($result) . '">Name or Address</option>';
                                $result = $connection->query("SELECT * FROM characters");
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['uuid'] . '">' . $row['name'] . ' - ' . $row['age'] . ' - ' . $row['address'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <small id="characterHelp" class="form-text" style="color:red; display:none;">Please to a person to cite.</small>

                            <div id="ticketForm">
                                <div class="form-group">
                                    <label for="reasonText">Ticket Reason</label>
                                    <input type="text" class="form-control" id="reasonText" placeholder="Reason">
                                    <small id="reasonHelp" class="form-text" style="color:red; display:none;">Cannot be left blank.</small>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Fine Amount</label>
                                    <div class="form-group">
                                      <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                          <span class="input-group-text">$</span>
                                        </div>
                                        <input id="amountText" type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                                        <div class="input-group-append">
                                          <span class="input-group-text">.00</span>
                                        </div>
                                      </div>
                                    </div>
                                    <small id="amountHelp" class="form-text" style="color:red; display:none">Cannot be left blank.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-bottom:10px;">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="issueTicket();">Issue Ticket</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="content">
            <div id = "alert_placeholder"></div>
            <div class="center-block" style="text-align: center;">
                <div class="center-block" style="margin: 0 auto; text-align: center; width: inherit; display: inline-block;">
                    <button class="btn btn-success center-block" onclick="updateStatus(1);" id="status_1">10-8</button>
                    <button href="#" class="btn btn-warning center-block" onclick="updateStatus(2);" id="status_2">10-6</button>
                    <button href="#" class="btn btn-danger center-block" onclick="updateStatus(0);" id="status_0">10-7</button>
                </div>
            </div>
            <div>
                <h2 style="width:50%;display:inline-block;" class="text-center">Active Units</h2>
                <h2 style="width:49%;display:inline-block;" class="text-center">Active Bolos</h2>
                <div style="max-height:280px; overflow:auto; display:inline-block; width:50%; float:left;">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Unit Number</th>
                            <th scope="col">Status</th>
                            <th scope="col">Update</th>
                          </tr>
                        </thead>
                        <tbody id="unitsTableBody">
                        </tbody>
                    </table>
                </div>
                <div style="max-height:280px; overflow:auto; display:inline-block; width:49%; float:right;">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">License Plate</th>
                            <th scope="col">Make/Model</th>
                            <th scope="col">Color</th>
                            <th scope="col">Delete</th>
                          </tr>
                        </thead>
                        <tbody id="bolosTableBody">
                        </tbody>
                    </table>
                </div>
                <h2 class="text-center" style="clear:both">Active Calls</h2>
                <div style="max-height:280px; overflow:auto; width:100%;">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Description</th>
                            <th scope="col">Assigned Units</th>
                            <th scope="col">Delete</th>
                          </tr>
                        </thead>
                        <tbody id="callsTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
