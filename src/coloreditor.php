<?php
$files = scandir('./img/front');
if (!empty($_POST['import'])) {
    $decode = json_decode($_POST['import']);
    $_POST['file'] = array_pop($decode);
    $_POST['from'] = [];
    $_POST['to'] = [];
    $_POST['fuzz'] = [];
    for ($i = 0; $i < count($decode); $i++) {
        $_POST['from'][] = $decode[$i]->from;
        $_POST['to'][] = $decode[$i]->to;
        $_POST['fuzz'][] = $decode[$i]->fuzz;
    }
    $_POST['passes'] = count($decode);
}
if (!empty($_POST['from'])) {
    $tmp = "./img/front/" . $_POST['file'];
    $tmp_back = "./img/back/" . $_POST['file'];
    for ($i = 0; $i < $_POST['passes']; $i++) {
        $to = "./tmp/front/" . $_POST['file'];
        $res = exec("convert " . $tmp . " -fuzz " . $_POST['fuzz'][$i] . "% -fill \"" . $_POST['to'][$i] . "\" -opaque \"" . $_POST['from'][$i] . "\" " . $to);
        $tmp = $to;
        $to_back = "./tmp/back/" . $_POST['file'];
        $res_back = exec("convert " . $tmp_back . " -fuzz " . $_POST['fuzz'][$i] . "% -fill \"" . $_POST['to'][$i] . "\" -opaque \"" . $_POST['from'][$i] . "\" " . $to_back);
        $tmp_back = $to_back;
        //echo "convert ./img/front/" . $_POST['file'] . " -fuzz " . $_POST['range'] . "% -fill \"" . $_POST['to'] . "\" -opaque \"" . $_POST['from'] . "\" ./tmp/" . $tmp . ".gif";
    }
    $data = array();
    for ($i = 0; $i < $_POST['passes']; $i++) {
        $data[] = array(
            'from' => $_POST['from'][$i],
            'to' => $_POST['to'][$i],
            'fuzz' => $_POST['fuzz'][$i],
        );
    }
    $data[] = $_POST['file'];
    echo '<script>var data = ' . json_encode($data) . ';</script>';
}
?>
<form action='#' method="post">
    <select name='file'>
        <?php
        for ($i = 0; $i < count($files); $i++) {
            echo "<option value='$files[$i]'>$files[$i]</option>";
        }
        ?>
        ?>
    </select>
    <input type="submit" value="Ok" />
</form>
<?php
if (!empty($_POST['file'])) {
?>
    <form action="#" method="post">
        <div style="display: flex;">
            <div style="display: flex; flex-flow: column; text-align: center;">
                <img src="img/front/<?php echo $_POST['file']; ?>" />
                Normal
            </div>
            <div style="display: flex; flex-flow: column; text-align: center;">
                <img src="img/front-shiny/<?php echo $_POST['file']; ?>" />
                Shiny
            </div>
            <?php
            if (!empty($tmp)) {
                echo '<div style="display: flex; flex-flow: column; text-align: center;"><img src="' . $tmp . '" />Recolored</div>';
            }
            if (!empty($tmp_back)) {
                echo '<div style="display: flex; flex-flow: column; text-align: center;"><img src="' . $tmp_back . '" />Recolored back</div>';
            }
            ?>
        </div>
        <p>Choose your monster's colors:</p>

        <div id="coloreditor"></div>

        <input type="hidden" name="file" value="<?php echo $_POST['file']; ?>" />
        <input type="submit" value="Apply" />
    </form>
<?php
}
?>
<form action="#" method="post">
    <textarea name="import"><?php echo json_encode($data); ?></textarea>
    <input type="submit" value="Load" />
</form>
<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
<script src="coloreditor.js"></script>