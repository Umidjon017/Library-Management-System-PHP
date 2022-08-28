<?php
// Category.php

include "../database_connection.php";
include "../functions.php";

if (!is_admin_login())
{
    header('Location: ../admin_login.php');
}

$message = '';
$error = '';
if (isset($_POST['add_category']))
{
    $formdata = array();
    if (empty($_POST['category_name']))
    {
        $error .= "<li>Category Name is required!</li>";
    }
    else {
        $formdata['category_name'] = trim($_POST['category_name']);
    }
    if ($error == '')
    {
        $query = "
        SELECT * FROM lms_category
        WHERE category_name = '".$formdata['category_name']."'
        ";
        $statement = $connect->prepare($query);
        $statement->execute();
        if ($statement->rowCount() > 0)
        {
            $error .= "<li>Category Name is already exists</li>";
        }
        else {
            $data = array(
                ':category_name' => $formdata['category_name'],
                ':category_status' => 'Enable',
                ':category_created_at' => get_date_time($connect)
            );
            $query = "
            INSERT INTO lms_category
            (category_name, category_status, category_created_at)
            VALUES (:category_name, :category_status, :category_created_at)
            ";
            $statement = $connect->prepare($query);
            $statement->execute($data);
            header('Location: category.php?msg=add');
        }
    }
}

if(isset($_POST["edit_category"]))
{
	$formdata = array();

	if(empty($_POST["category_name"]))
	{
		$error .= '<li>Category Name is required</li>';
	}
	else
	{
		$formdata['category_name'] = $_POST['category_name'];
	}

	if($error == '')
	{
		$category_id = convert_data($_POST['category_id'], 'decrypt');

		$query = "SELECT * FROM lms_category 
                    WHERE category_name = '".$formdata['category_name']."'
                    AND category_id != '".$category_id."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Category Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':category_name'		=>	$formdata['category_name'],
				':category_updated_at'	=>	get_date_time($connect),
				':category_id'			=>	$category_id
			);

			$query = "UPDATE lms_category 
                        SET category_name = :category_name, 
                            category_updated_at = :category_updated_at  
                        WHERE category_id = :category_id
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:category.php?msg=edit');
		}
	}
}

$query = "
    SELECT * FROM lms_category
    ORDER BY category_name ASC  
";

$statement = $connect->prepare($query);
$statement->execute();

include "../header.php";
?>

<div class="container-fluid py-4" style="min-height: 700px;">

    <?php if (isset($_GET['action'])): ?>

        <?php if ($_GET['action'] == 'add'): ?>

        <h1>Category Management</h1>
        <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
            <li class="breadcrumb-item"><a href="<?=base_url()?>admin/index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>admin/category.php">Category Management</a></li>
            <li class="breadcrumb-item"><a href="<?=base_url()?>admin/category.php?action=add">Add Category</a></li>
        </ol>
        <div class="row">
            <div class="col-md-6">
                <?php
                if ($error != '')
                {
                    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
                                    <li class='list-unstyled'>$error</li>
                                    <button type='button' class='btn btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>";
                }
                ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i> Add New Category
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="category_name" class="label-form">Category Name</label>
                                <input type="text" name="category_name" id="category_name" class="form-control">
                            </div>
                            <div class="mt-4 mb-0">
                                <input type="submit" name="add_category" value="Add" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

        <?php
            $category_id = convert_data($_GET['code'], 'decrypt');
            if ($category_id > 0)
            {
                $query = "SELECT * FROM lms_category WHERE category_id = '$category_id'";
                $category_result = $connect->query($query);
                foreach ($category_result as $category_row): ?>

                    <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                        <li class="breadcrumb-item"><a href="<?=base_url()?>admin/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?=base_url()?>admin/category.php">Category Management</a></li>
                        <li class="breadcrumb-item">Edit Category</li>
                    </ol>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-user-edit"></i> Edit Category Details
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST">
                                        <div class="mb-3">
                                            <label for="category_name" class="label-form">Category Name</label>
                                            <input type="text" name="category_name" id="category_name" value="<?=$category_row['category_name']?>" class="form-control">
                                        </div>
                                        <div class="mt-4 mb-0">
                                            <input type="hidden" name="category_id" value="<?=$_GET['code']?>">
                                            <input type="submit" name="edit_category" value="Edit" class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                endforeach;
            }
        ?>

    <?php else: ?>

            <h1>Category Management</h1>
            <ol class="breadcrumb mt-4 mb-4 bg-light p-2 border">
                <li class="breadcrumb-item"><a href="<?=base_url()?>admin/index.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?=base_url()?>admin/category.php">Category Management</a></li>
            </ol>

            <?php if (isset($_GET['msg'])) {
                    if ($_GET['msg'] == 'add') {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <li class='list-unstyled'>New Category Name Added</li>
                                <button class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                    }

                    if ($_GET['msg'] == 'edit') {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <li class='list-unstyled'>Category Data Edited</li>
                                <button class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                    }
                } ?>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-md-6">
                            <i class="fas fa-table me-1"></i> Category Management
                        </div>
                        <div class="col col-md-6" align="right">
                            <a href="category.php?action=add" class="btn btn-sm btn-success">Add</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple">
                        <thead>
                        <tr>
                            <th>Category Name</th>
                            <th class="text-center">Status</th>
                            <th>Created On</th>
                            <th>Updated On</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Category Name</th>
                            <th class="text-center">Status</th>
                            <th>Created On</th>
                            <th>Updated On</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php if ($statement->rowCount() > 0): ?>
                            <?php foreach ($statement->fetchAll() as $row): ?>
                                <?php $category_status = '';
                                    if ($row['category_status'] == 'Enable') {
                                        $category_status .= "<span class='badge bg-success'>".$row['category_status']."</span>";
                                    } ?>
                                <tr>
                                    <td><?=$row['category_name']?></td>
                                    <td class="text-center"><?=$category_status?></td>
                                    <td><?=$row['category_created_at']?></td>
                                    <td><?=$row['category_updated_at']?></td>
                                    <td><a href="category.php?action=edit&code=<?=convert_data($row['category_id'])?>" class="btn btn-sm btn-primary">Edit</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php
    endif;
?>

<?php
include "../footer.php";
?>
