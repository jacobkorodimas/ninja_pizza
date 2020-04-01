<?php
    include('config/db_connect.php');


    $errors = array('ingredients' => '');

    if(isset($_POST['submit'])){

        // check ingredients
		if(empty($_POST['ingredients'])){
			$errors['ingredients'] = 'At least one ingredient is required';
		} else{
			$ingredients = $_POST['ingredients'];
			if(!preg_match('/^([a-zA-Z\s]+)(,\s*[a-zA-Z\s]*)*$/', $ingredients)){
				$errors['ingredients'] = 'Ingredients must be a comma separated list';
			}
		}

        if(array_filter($errors)){
			//echo 'errors in form';
		} else {
            // escape sql chars
            $id_to_edit = mysqli_real_escape_string($conn, $_POST['id']);
			$ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);

			// create sql
			$sql = "UPDATE pizzas SET ingredients = '$ingredients' WHERE id = '$id_to_edit'";

			// save to db and check
			if(mysqli_query($conn, $sql)){
				// success
				header('Location: index.php');
			} else {
				echo 'query error: '. mysqli_error($conn);
			}
			
		}

    }

    if(isset($_GET['id'])){

        //set $id var and escape sql characters
        $id = mysqli_real_escape_string($conn, $_GET['id']);

        //make sql
        $sql = "SELECT * FROM pizzas WHERE id = $id";

        //get result
        $result = mysqli_query($conn, $sql);

        //fetch result
        $pizza = mysqli_fetch_assoc($result);
        
        //free result and close connection
        mysqli_free_result($result);
		mysqli_close($conn);
    }


?>


<!DOCTYPE html>
<html lang="en">
    <?php include('templates/header.php'); ?>

    <div class="container center grey-text">
    <?php if($pizza): ?>
		<form action="edit.php" method="POST">
            <label>Ingredients (comma separated)</label>
            <input type="hidden" name="id" value="<?php  echo htmlspecialchars($pizza['id']) ?>"">
            <input type="text" name="ingredients"  autocomplete = "off" value="<?php echo htmlspecialchars($pizza['ingredients']) ?>">
			<div class="red-text"><?php echo $errors['ingredients']; ?></div>
			<div class="center">
				<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
			</div>
        </form>
        <?php else: ?>
			<h5>No such pizza exists.</h5>
		<?php endif ?>
	</div>

    <?php include('templates/footer.php'); ?>

</html>