    <section class="content-header">
      <h1>
        Pengelolaan Hak Akses Aplikasi
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Secret</a></li>
        <li class="active">Hak Akses</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <form action="<?php echo base_url('administrator/tambah_hak_akses'); ?>" method="POST">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Hak Akses Aplikasi</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
              </div>

              <div class="box-body">
                <div class='col-md-6'>

                  <div class='form-group'>
                    <label>Menu Utama</label>
                    <select class="form-control select2" style='width: 100%;' name="menu1">
                      <?php
                      foreach ($menu1 as $key => $value) {
                        ?><option value="<?php echo $key; ?>"><?php echo $value['nama_menu1']; ?></option><?php
                      }
                      ?>
                    </select>
                  </div>

                  <div class='form-group'>
                    <label>Sub Menu Utama</label>
                    <select class="form-control select2" name="menu2[]" multiple="multiple" data-placeholder="Masukkan Sub Menu (Bisa lebih dari satu)" style='width: 100%;'>
                      <option value="0">Tidak Pilih Menu</option>
                      <?php
                      foreach ($menu2 as $key => $value) {
                        ?><option value="<?php echo $key; ?>"><?php echo $value['nama_menu2']; ?></option><?php
                      }
                      ?>
                    </select>
                  </div>

                  <div class='form-group'>
                    <label>Sub Menu CRUD</label>
                    <select class="form-control select2" name="menu_crud[]" multiple="multiple" data-placeholder="Masukkan Sub Menu CRUD (Bisa lebih dari satu)">
                      <option value="0">Tidak Pilih Menu</option>
                      <?php
                      foreach ($menu_crud as $key => $value) {
                        ?><option value="<?php echo $key; ?>"><?php echo $value['nama_menu_crud']; ?></option><?php
                      }
                      ?>
                    </select>
                  </div>

                </div>
                <div class="col-md-6">

                  <div class='form-group'>
                    <label>Posisi Subdit</label>
                    <select class='form-control select2' style='width: 100%;' name="posisi_subdit">
                      <?php
                      foreach ($posisi_subdit as $key => $value) {
                        ?><option value="<?php echo $value['id_posisi_subdit']; ?>"><?php echo $value['nama_posisi_subdit']; ?></option><?php
                      }
                      ?>
                    </select>
                  </div>

                  <div class='form-group'>
                    <label>Distrik</label>
                    <select class='form-control select2' style='width: 100%;' name="distrik">
                      <?php
                      foreach ($distrik as $key => $value) {
                        ?><option value="<?php echo $value['id_distrik']; ?>"><?php echo $value['nama_distrik']; ?></option><?php
                      }
                      ?>
                    </select>
                  </div>

                </div>
              </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right btn-lg">Simpan</button>
              </div>
            </div>
          </form>
        </div>
      </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
<script>
  $(function () {
    <?php
      if ($this->session->flashdata('entry') == 1)
      {
        ?>alert("Berhasil menambahkan hak akses baru");<?php
      }
      else if ($this->session->flashdata('entry') == 2)
      {
        ?>alert("Gagal menambahkan hak akses");<?php
      }
      else if ($this->session->flashdata('entry') == 3)
      {
        ?>alert("Menu yang dipilih belum dibuat di aplikasi");<?php
      }
    ?>
    //Initialize Select2 Elements
    $('.select2').select2()

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })
  })
</script>