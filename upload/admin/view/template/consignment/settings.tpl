<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <?php if ($error) { ?>
  <div class="warning"><?php echo $error; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><?php echo $text_app_id; ?></td>
            <td><input type="text" name="app_id" value="<?php echo $app_id; ?>" />
                <?php if ($error_app_id) { ?>
                  <span class="error"><?php echo $error_app_id; ?></span>
                <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?php echo $text_app_key; ?></td>
            <td><input type="text" name="app_key" value="<?php echo $app_key; ?>" />
                <?php if ($error_app_key) { ?>
                  <span class="error"><?php echo $error_app_key; ?></span>
                <?php } ?>
            </td>
          </tr>
          <tr>
            <td><?php echo $text_consignment_prefix; ?></td>
            <td><input type="text" name="consignment_prefix" value="<?php echo $consignment_prefix; ?>" />
                <?php if ($error_consignment_prefix) { ?>
                  <span class="error"><?php echo $error_consignment_prefix; ?></span>
                <?php } ?>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>