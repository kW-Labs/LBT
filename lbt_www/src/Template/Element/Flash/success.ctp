<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<script>
    $(function () {
        new PNotify({
            title: 'Success',
            text: '<?= $message ?>'
        });
    });
</script>