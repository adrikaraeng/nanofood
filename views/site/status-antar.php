<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title='Status Pengiriman';
?>
<p>
	<span class="note-danger">Konfirmasi di kontak kami jika pesanan anda telah diterima.</span>
</p>
<?php if($model->status=='Booking'):?>
	<div class='color-circle'>
		<span class='fa fa-file-text'></span><span class='nomor'>1</span>
		<p>Pemesanan</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-eye'></span><span class='nomor'>2</span>
		<p>Diproses</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-money'></span><span class='nomor'>3</span>
		<p>Verifikasi</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-send'></span><span class='nomor'>4</span>
		<p>Pengantaran</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-flag'></span><span class='nomor'>5</span>
		<p>Selesai</p>
	</div>
<?php elseif($model->status == 'Diproses'):?>
	<div class='color-circle'>	
		<span class='fa fa-file-text'></span><span class='nomor'>1</span>
		<p>Pemesanan</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-eye'></span><span class='nomor'>2</span>
		<p>Diproses</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-money'></span><span class='nomor'>3</span>
		<p>Verifikasi</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-send'></span><span class='nomor'>4</span>
		<p>Pengantaran</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-flag'></span><span class='nomor'>5</span>
		<p>Selesai</p>
	</div>
<?php elseif($model->status == 'Verifikasi'):?>
	<div class='color-circle'>	
		<span class='fa fa-file-text'></span><span class='nomor'>1</span>
		<p>Pemesanan</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-eye'></span><span class='nomor'>2</span>
		<p>Diproses</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-money'></span><span class='nomor'>3</span>
		<p>Verifikasi</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-send'></span><span class='nomor'>4</span>
		<p>Pengantaran</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-flag'></span><span class='nomor'>5</span>
		<p>Selesai</p>
	</div>
<?php elseif($model->status == 'Pengantaran'):?>
	<div class='color-circle'>	
		<span class='fa fa-file-text'></span><span class='nomor'>1</span>
		<p>Pemesanan</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-eye'></span><span class='nomor'>2</span>
		<p>Diproses</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-money'></span><span class='nomor'>3</span>
		<p>Verifikasi</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-send'></span><span class='nomor'>4</span>
		<p>Pengantaran</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-flag'></span><span class='nomor'>5</span>
		<p>Selesai</p>
	</div>
<?php elseif($model->status == 'Selesai'):?>
	<div class='color-circle'>	
		<span class='fa fa-file-text'></span><span class='nomor'>1</span>
		<p>Pemesanan</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-eye'></span><span class='nomor'>2</span>
		<p>Diproses</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-money'></span><span class='nomor'>3</span>
		<p>Verifikasi</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-send'></span><span class='nomor'>4</span>
		<p>Pengantaran</p>
	</div>
	<div class='color-circle'>
		<span class='fa fa-flag'></span><span class='nomor'>5</span>
		<p>Selesai</p>
	</div>
<?php else:?>
	<div class='whiteblack-circle'>
		<span class='fa fa-file-text'></span>
		<p>Pemesanan</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-eye'></span>
		<p>Diproses</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-money'></span>
		<p>Verifikasi</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-send'></span>
		<p>Pengantaran</p>
	</div>
	<div class='whiteblack-circle'>
		<span class='fa fa-flag'></span>
		<p>Selesai</p>
	</div>
<?php endif;?>
<div style="clear:left;"></div>