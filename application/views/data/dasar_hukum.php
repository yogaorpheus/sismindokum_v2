<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dasar Hukum
      </h1>
      <ol class="breadcrumb">
        <li><i class="fa fa-file-text-o"></i> Dasar Hukum</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <a href="<?php echo base_url('dasar_hukum/tambah_dasar_hukum'); ?>">
                <button class="btn btn-primary pull-left"><i class="glyphicon glyphicon-plus"></i> Tambah Dasar Hukum</button>
              </a>
              <a href="<?php echo base_url('dasar_hukum/download'); ?>">
                <button class="btn btn-success pull-right"><i class="glyphicon glyphicon-download-alt"></i> Download Data</button>
              </a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="tabel1" class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Sub Menu</th>
                  <th>Nama Jenis</th>
                  <th>Kode</th>
                  <th>Keterangan</th>
                  <th>Dibuat oleh</th>
                  <th>Pengaturan</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $no = 1; 
                foreach ($dasar_hukum as $key => $onedata) { 
                ?>
                <tr>
                  <td style="vertical-align: middle;"><?php echo $no++; ?></td>
                  <td style="vertical-align: middle;"><?php echo $onedata['nama_menu2']; ?></td>
                  <td style="vertical-align: middle;"><?php echo $onedata['nama_sub_jenis_sertifikat']; ?></td>
                  <td style="vertical-align: middle;"><?php echo $onedata['kode_dasar_hukum']; ?></td>
                  <td style="vertical-align: middle;"><?php echo $onedata['keterangan_dasar_hukum']; ?></td>
                  <td style="vertical-align: middle;"><?php echo $onedata['nama_lengkap_pegawai']; ?></td>
                  <td width="120px;">
                    <div class="row">
                      <div class="col-md-6">
                        <a href="<?php echo base_url('dasar_hukum/edit_dasar_hukum').'/'.$onedata['id_dasar_hukum']; ?>">
                          <button type="button" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-pencil"></i></button>
                        </a>
                      </div>
                      <div class="col-md-6">
                        <a href="#" class="Delete" id="<?php echo $onedata['id_dasar_hukum']; ?>">
                          <button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal_delete"><i class="glyphicon glyphicon-trash"></i></button>
                        </a>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php }
                ?>
                </tbody>
                <tfoot>
                <tr>
                  <th>No.</th>
                  <th>Jenis Sertifikat</th>
                  <th>Kode</th>
                  <th>Keterangan</th>
                  <th>Dibuat oleh</th>
                  <th>Pengaturan</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="modal fade" id="modal_delete">
        <form id="delete_dasar_hukum" action="<?php echo base_url('dasarhukumcontroller/delete_dasar_hukum'); ?>" method="POST">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">HAPUS DATA</h4>
                <input type="hidden" id="id_data" name="id_delete" value="">
              </div>
              <div class="modal-body">
                <p>Apakah anda yakin ingin menghapus data ini?</p>
              </div>
              <div class="modal-footer">
                <a id="delete_yes"><button type="submit" class="btn btn-danger pull-left">Iya, Hapus</button></a>
                <button type="button" class="btn btn-success pull-right" data-dismiss="modal" id="delete_no">Tidak</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </form>    
      </div>
      
    </section>
    <!-- /.content -->
    <script>
      var delete_id = "";

      $(function () {
        // $('#example1').DataTable()
        // $('#example2').DataTable({
        //   'paging'      : true,
        //   'lengthChange': false,
        //   'searching'   : false,
        //   'ordering'    : true,
        //   'info'        : true,
        //   'autoWidth'   : false
        // })
        $('#tabel1').DataTable()

        $(document).on("click", ".Delete", function() {
          delete_id = $(this).attr('id');
          $("#id_data").val(delete_id);
        })

        <?php
        if ($this->session->flashdata('error') == 1) {
          echo "alert('Data Lisensi berhasil dihapus');";
        }
        else if ($this->session->flashdata('error') == 2) {
          echo "alert('Data Lisensi gagal dihapus');";
        }
        ?>
      })
    </script>