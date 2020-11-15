<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\ArrayHelper;
use yii\db\Query;

$this->title = Yii::t('app', 'Bukti Bayar');
$connection = \Yii::$app->db;
$wilSQL = $connection->createCommand("SELECT * FROM wilayah WHERE aktivasi='Aktif' ORDER BY nama ASC");
$bankSQL = $connection->createCommand("SELECT * FROM bank WHERE aktivasi='Aktif' ORDER BY bank ASC")->queryAll();
$transaksiSQL = $connection->createCommand("SELECT * FROM transaksi WHERE ip='$model->ip' AND no_transaksi='$model->no_transaksi' ORDER BY nama_produk ASC")->queryAll();

?>
<?php if($model != NULL):?>
    <div class="konfirmasi-detail-form">
        	<p style="text-align:center;margin-top:-10px;margin-bottom:-4px;font-size:1.3em;">Kode Pesanan : <b><?=$model->no_transaksi?></b></p>
        <?php $form = ActiveForm::begin(['id'=>'id-konnfirmasi', 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <p>
                <span class='note-danger'>*)Simpan Kode Pesanan anda, Lakukan pembayaran sebelum batas waktu pembayaran dan nominal transaksi sesuai dengan Total Pembayaran.</span>
			</p>
			<fieldset>
				<legend>Batas waktu pembayaran</legend>
        	<p>
        		Batas Pembayaran : <b><?=date('d/M/Y H:i:s', strtotime($model->tanggal_expired))." WIB";?></b>
        		<?= Html::a(Yii::t('app', "Kirim Bukti Pembayaran <span class='fa fa-arrow-down'></span>"), '#bukti-bayar', ['class' => 'btn btn-success']) ?>
			</p>
			</fieldset>
            <br>

            <fieldset>
                <legend>Pilih <b>Bank</b> untuk transaksi</legend>
                <p>
                    <table>
                        <tr class="tr-judul">
                            <td>Nama</td>
                            <td>No. Rekening</td>
                            <td>Atas Nama</td>
                        </tr>
                        <?php $i=0;foreach($bankSQL as $b => $bank):?>
                        <?php if($i % 2 == 0):?>
                            <tr class="tr-genap">
                                <td><?=$bank['bank']?></td>
                                <td><?=$bank['no_rek']?></td>
                                <td><?=$bank['rek_a_n']?></td>
                            </tr>
                        <?php else:?>
                            <tr clas="tr-ganjil">
                                <td><?=$bank['bank']?></td>
                                <td><?=$bank['no_rek']?></td>
                                <td><?=$bank['rek_a_n']?></td>
                            </tr>
                        <?php endif;?>
                        <?php endforeach;?>
                    </table>
                </p>
            </fieldset>
            <br>
            <fieldset>
                <legend>Pelanggan</legend>
        		<p><b><?=$model->nama_pelanggan?>, (<?=$model->no_telepon?>)</b></p>
            </fieldset>
            <br>
            <fieldset>
                <legend>Alamat Pengiriman</legend>

                <p><?=$model->wilayah0->nama;?></p>

                <?= $form->field($model, 'alamat_lengkap')->textarea(['placeholder'=>'Alamat Lengkap Pengiriman (*)','rows' => 3,'style'=>'resize:none;','readOnly'=>true])->label(false) ?>
            </fieldset>
            <br>
            <fieldset>
            	<legend>Keranjang Belanja</legend>
            	<table style="width:100%;">
        			<tr class='tr-judul'>
        				<td>Nama Produk</td>
        				<td style="text-align:right;">Harga</td>
        				<td style="text-align:center;">Jumlah</td>
        				<td style="text-align:right;">Jumlah x Harga</td>
        			</tr>
            		<?php $i=0;$total = 0;$diskon=0;$subtotal=0;foreach($transaksiSQL as $t => $val):?>
            			<?php 
			                if($val['diskon_jumlah_beli'] != 0 && $val['free_diskon'] != 0 || $val['diskon_jumlah_beli'] != '' && $val['free_diskon'] != '' || $val['diskon_jumlah_beli'] != NULL && $val['free_diskon'] != NULL):
			                    $t_ = 0;
			                    if($val['jumlah'] >= $val['diskon_jumlah_beli']):
			                        $t_ = (floor($val['jumlah'] / $val['diskon_jumlah_beli'])*$val['free_diskon'])*$val['harga_jual'];
			                        $diskon += $t_;
			                    endif;
			                endif;
			            ?>
            			<?php $jumlah = $val['jumlah']*$val['harga_jual'];?>
            			<?php if($i % 2 == 0):?>
		            		<tr class='tr-ganjil'>
		            			<td><?=$val['nama_produk']?></td>
		            			<td style="text-align:right;"><?=number_format($val['harga_jual'],0,',','.').",00";?></td>
		            			<td style="text-align:center;"><?=$val['jumlah']?></td>
		            			<td style="text-align:right;"><?php
		            				echo number_format($jumlah,0,',','.').",00";
		            			?></td>
		            		</tr>
		            	<?php else:?>
		            		<tr class='tr-genap'>
		            			<td><?=$val['nama_produk']?></td>
		            			<td style="text-align:right;"><?=number_format($val['harga_jual'],0,',','.').",00";?></td>
		            			<td style="text-align:center;"><?=$val['jumlah']?></td>
		            			<td style="text-align:right;"><?php
		            				echo number_format($jumlah,0,',','.').",00";
		            			?></td>
		            		</tr>
	            		<?php endif;?>
	            		<?php $subtotal += $jumlah;?>
            		<?php $i++; endforeach;?>
	            	<?php $total = $subtotal - $diskon;?>
            		<tr class="tr-footer" style="text-align:right;">
            			<td colspan="3">Subtotal</td>
            			<td><?=number_format($subtotal,0,',','.').",00"?></td>
            		</tr>
            		<tr class="tr-footer" style="text-align:right;">
            			<td colspan="3">Ongkos Kirim</td>
            			<td><?=number_format($model->wilayah0->harga,0,',','.').",00"?></td>
            		</tr>
            		<tr class="tr-footer" style="border-bottom:1px solid #fff;text-align:right;">
            			<td colspan="3">Diskon</td>
            			<td><?=number_format($diskon,0,',','.').",00"?></td>
            		</tr>
            		<tr class="tr-footer" style="text-align:right;">
            			<?php $total = $total + $model->wilayah0->harga;?>
            			<td colspan="3">Total Pembayaran</td>
            			<td><?=number_format($total,0,',','.').",00"?></td>
            		</tr>
            	</table>
            </fieldset>
            <br>
            <fieldset id="bukti-bayar">
            	<legend>Bukti Bayar</legend>
		        <?=
		            $form->field($model, 'struk_bukti')->widget(FileInput::classname(), [
		                'options' => [
		                    'id' => 'struk-id',
		                    'class' => 'gambar-struk',
		                    'style' => "float:left;",
		                    'multiple' => false,
		                    'accept' => 'image/*',
		                ],

		                'pluginOptions' => [
		                    'allowedFileExtensions' => ['jpg','png','jpeg'],
		                    'previewFileType' => 'image',
		                    'showUpload' => false,
		                    'showPreview' => false,
		                    'showRemove' => false,
		                    'showCaption' => true,
		                    'maxFileSize'=>2048,
		                    'browseIcon' => '<i class="fa fa-camera" style="width:50px;"></i>',
		                    'browseLabel' => '',
		                ]
		            ])->label(false);
		        ?> 
            </fieldset>
            <div class="form-group" style="clear:left;text-align:center;margin-top:10px;margin-bottom:0px;">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Kirim bukti bayar') : Yii::t('app', 'Kirim bukti bayar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','data-confirm'=>'Silahkan dilanjutkan jika data telah benar']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php else:?>
	<span style="color:#bf0005;"><b><i>Tidak ada transaksi</i></b></span>
<?php endif;?>