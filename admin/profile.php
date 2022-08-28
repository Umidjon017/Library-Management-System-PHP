<?php
include "../database_connection.php";
include "../functions.php";
if (!is_admin_login())
{
    header('Location: ../admin_login.php');
}
include "../header.php";

global $connect;

$message = '';
$error = '';
if (isset($_POST['edit_admin']))
{
    $formData = array();
    if (empty($_POST['admin_email']))
    {
        $error .= "<li>Email Address is required</li>";
    }
    else {
        if (!filter_var($_POST['admin_email'], FILTER_VALIDATE_EMAIL))
        {
            $error .= "<li>Invalid Email Address</li>";
        }
        else {
            $formData['admin_email'] = $_POST['admin_email'];
        }
    }

    if (empty($_POST['admin_password']))
    {
        $error .= "<li>Password is required</li>";
    }
    else {
        $formData['admin_password'] = $_POST['admin_password'];
    }

    if ($error == '')
    {
        $admin_id = $_SESSION['admin_id'];
        $data = array(
            ':admin_email' => $formData['admin_email'],
            ':admin_password' => $formData['admin_password'],
            ':admin_id' => $admin_id
        );
        $query = "
            UPDATE lms_admin
                SET admin_email = :admin_email,
                    admin_password = :admin_password
                WHERE admin_id = :admin_id
        ";
        $statement = $connect->prepare($query);
        $statement->execute($data);
        $message .= "Data successfully saved!";
    }
}

$query = "SELECT * FROM lms_admin WHERE admin_id = '".$_SESSION['admin_id']."'";
$result = $connect->query($query);

$message1 = '';
if (isset($_POST['check_admin']))
{
    $formData1 = array();
    if (empty($_POST['admin_email_check']))
    {
        $message1 .= "<li>Email Address is required</li>";
    }
    else {
        if (!filter_var($_POST['admin_email_check'], FILTER_VALIDATE_EMAIL))
        {
            $message1 .= "<li>Invalid Email Address</li>";
        }
        else {
            $formData1['admin_email_check'] = $_POST['admin_email_check'];
        }
    }

    if (empty($_POST['admin_password_check']))
    {
        $message1 .= "<li>Password is required</li>";
    }
    else {
        $formData1['admin_password_check'] = $_POST['admin_password_check'];
    }

    if ($message1 == '')
    {
        $data1 = array(
            ':admin_email_check' => $formData1['admin_email_check']
        );
        $query1 = "SELECT * FROM lms_admin WHERE admin_email = :admin_email_check";
        $statement1 = $connect->prepare($query1);
        $statement1->execute($data1);

        if ($statement1->rowCount() > 0)
        {
            foreach ($statement1->fetchAll() as $row1)
            {
                if ($row1['admin_password'] == $formData1['admin_password_check'])
                {
                    $_SESSION['admin_id'] = $row1['admin_id'];
                    ?>

                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Edit Your Profile Details</h1>
                        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border rounded">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active"><a href="profile.php">Profile</a></li>
                        </ol>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fa fa-user-edit"></i> Edit your profile details
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($result as $row): ?>
                                        <form action="" method="post">
                                            <div class="mb-3">
                                                <label class="label-form">Email address</label>
                                                <input type="text" name="admin_email" id="admin_email" class="form-control" value="<?= $row['admin_email'] ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="label-form">Password</label>
                                                <input type="text" name="admin_password" id="admin_password" class="form-control" value="<?= $row['admin_password'] ?>">
                                            </div>
                                            <div class=" mt-4 mb-0">
                                                <input type="submit" name="edit_admin" class="btn btn-primary" value="Edit"">
                                            </div>
                                        </form>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                else {
                    $message1 .= "<li>Wrong Password</li>";
                }
            }
        }
        else {
            $message1 .= "<li>Wrong Email Address</li>";
        }
    }
}
?>

<?php if (!isset($_POST['check_admin'])): ?>

    <div class="container-fluid px-4">
        <?php
        if ($error != '') {
            echo "<div class='container-fluid pt-4'>
                    <div class='alert alert-danger alert-dismissible' role='alert'>
                        <ul class='list-unstyled'>$error</ul>
                        <button type='button' class='btn btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>";
        }
        if ($message != '') {
            echo "<div class='container-fluid pt-4'>
                    <div class='alert alert-success alert-dismissible' role='alert'>
                        <ul class='list-unstyled'>$message</ul>
                        <button type='button' class='btn btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>";
        }
        ?>
        <h1 class="mt-4">Confirm Your Profile Details</h1>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border rounded">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="profile.php">Profile</a></li>
        </ol>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fa fa-user-edit"></i> Confirm your profile details
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="label-form">Email address</label>
                                <input type="text" name="admin_email_check" id="admin_email_check" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Password</label>
                                <input type="text" name="admin_password_check" id="admin_password_check" class="form-control">
                            </div>
                            <div class=" mt-4 mb-0">
                                <input type="submit" name="check_admin" class="btn btn-primary" value="Confirm"">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($message1 != ''): ?>

    <div class="container-fluid px-4">
        <h1 class="mt-4">Confirm Your Profile Details</h1>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border rounded">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="profile.php">Profile</a></li>
        </ol>
        <div class="row">
            <div class="col-md-6">
                <?php
                if ($message1 != '')
                {
                    echo "<div class='alert alert-danger alert-dismissible'>
                            <ul class='list-unstyled'>$message1</ul>
                            <button class='btn btn-close' type='button' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
                }
                ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fa fa-user-edit"></i> Confirm your profile details
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="label-form">Email address</label>
                                <input type="text" name="admin_email_check" id="admin_email_check" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="label-form">Password</label>
                                <input type="text" name="admin_password_check" id="admin_password_check" class="form-control">
                            </div>
                            <div class=" mt-4 mb-0">
                                <input type="submit" name="check_admin" class="btn btn-primary" value="Confirm"">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include "../footer.php"; ?>