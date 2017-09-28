<?php require('getAllSchools.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>

    <title>School Match Tool</title>
    <meta charset='utf-8'>

    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet'>
    <link href='/css/styles.css' rel='stylesheet'>

</head>
<body>

    <h1><a href='/'>School Match Tool</a></h1>

    <form method='GET'>

        <div class='form-group'>
            <label for='grade'>Select Grade (required):</label>
            <input type='number' name='grade' min="1" max="12" id='grade' value='<?=$form->prefill('grade','')?>'>
        </div>

        <fieldset class='checkboxes'>
            <label>Select school type:</label>
            <br>
            <label><input type='checkbox' name='schoolTypes[]' value='Catholic' <?php if (strstr($schoolTypeResults, 'Catholic')) echo 'CHECKED'?>> Catholic</label>
            <label><input type='checkbox' name='schoolTypes[]' value='Public' <?php if (strstr($schoolTypeResults, 'Public')) echo 'CHECKED'?>> Public</label>
            <label><input type='checkbox' name='schoolTypes[]' value='Private' <?php if (strstr($schoolTypeResults, 'Private')) echo 'CHECKED'?>> Private</label>
        </fieldset>

        <label for='neighborhood'>Select neighborhood</label>
        <select name='neighborhood' id='neighborhood'>
            <option value='All'>All</option>
            <option value='East Boston' <?php if ($neighborhood == "East Boston") echo 'SELECTED'?>>East Boston</option>
            <option value='Dorchester' <?php if ($neighborhood == "Dorchester") echo 'SELECTED'?>>Dorchester</option>
            <option value='Allston/Brighton' <?php if ($neighborhood == "Allston/Brighton") echo 'SELECTED'?>>Allston/Brighton</option>
        </select>

        <div class='form-group'>
            <input type='submit' class='btn btn-primary btn-small' value='Filter schools'>
        </div>

    </form>

    <?php if (!empty($errors)) : ?>
        <div class='alert alert-danger'>
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?=$error?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif ?>

    <?php if ($form->isSubmitted() and empty($errors)) : ?>

        <div class='alert alert-info'>You searched for schools with grade: <strong><?=sanitize($grade)?></strong></div>
        <div class='alert alert-info'>You searched for schools with type: <strong><?=sanitize($schoolTypeResults)?></strong></div>
        <div class='alert alert-info'>You searched for schools with neighborhood: <strong><?=sanitize($neighborhood)?></strong></div>

        <?php if (!$haveResults) : ?>
            <div class='alert alert-warning'>Your search criteria did not match any schools.</div
        <?php endif; ?>

        <?php foreach ($schools as $name => $school) : ?>
            <div class='school'>
                <h2><?=$name?></h2>
                <ul>
                    <li>Type: <?=$school['type']?></li>
                    <li>Grade: <?=$school['gradeFloor']." - ".$school['gradeCeiling']?></li>
                    <li>Neighborhood: <?=$school['neighborhood']?></li>
                </ul>
            </div>
        <?php endforeach;  ?>

    <?php endif ?>




</body>
</html>
