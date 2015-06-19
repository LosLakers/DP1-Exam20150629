<?php
if (isset($error)) {
    ?>
    <div class="<?=get_message_type($error)?>">
        <h3 class="error-text">"<?= get_message($error) ?>"</h3>
    </div>
    <br/>
<?php
}
?>