<div class="w-100" <?php echo isset($cardStyle) ? $cardStyle : '' ?> style="min-height: 100vh;">
    <div class="card-body" style="padding-top: 0;">
        <?php $this->load->view($view) ?>
    </div>
</div>