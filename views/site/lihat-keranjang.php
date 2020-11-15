<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\TransaksiSearch;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Json;
use yii\db\Query;
$connection = \Yii::$app->db;

$this->title = Yii::t('app', 'Daftar belanja');
$this->params['breadcrumbs'][] = $this->title;

$wilSQL = $connection->createCommand("SELECT * FROM wilayah WHERE aktivasi='Aktif' ORDER BY nama ASC");

$cekAvSQL = $connection->createCommand("SELECT * FROM transaksi WHERE status='Booking1' AND ip='$ip'")->queryAll();

?>
<div class="transaksi-index">
  
<?php if($cekAvSQL != NULL):?>

<?php Pjax::begin(['id'=>'pjx-keranjang-index','enablePushState'=>false]); ?>  
    <?php 
        $amount = 0;
        $j_h = 0;
        $j_f = 0;
        $diskon = 0;
        $Tdiskon = 0;
         if (!empty($dataProvider->getModels())) {
            foreach ($dataProvider->getModels() as $key => $val) {
                $tmp = $val->harga_jual * $val->jumlah;
                $amount += $tmp;
                if($val->diskon_jumlah_beli != 0 && $val->free_diskon != 0 || $val->diskon_jumlah_beli != '' && $val->free_diskon != '' || $val->diskon_jumlah_beli != NULL && $val->free_diskon != NULL):
                    $t_ = 0;
                    if($val->jumlah >= $val->diskon_jumlah_beli):
                        $t_ = (floor($val->jumlah / $val->diskon_jumlah_beli)*$val->free_diskon)*$val->harga_jual;
                        $diskon += $t_;
                    endif;
                endif;
            }
            $Tdiskon = number_format($diskon,0,',','.').",00";
        }
        $Tamount = number_format($amount,0,',','.').",00";
    ?>

    <?php
        $this->registerJs("
            $('#pjx-hasil-jual').html('".$Tamount."');
            $('#pjx-hasil-diskon').html('".$Tdiskon."');
        ");
    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id'=>'pjx-keranjang','enablePushState'=>false]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            //'ip:ntext',
            //'nama_produk:ntext',
            [
                'attribute' => 'nama_produk',
                'format' => 'raw',
                'value' => function($model){
                    $nama = $model->nama_produk;
                    $satuan = $model->satuan;
                    $beli = $model->diskon_jumlah_beli;
                    $gratis = $model->free_diskon;

                    if($beli != null || $beli != '' && $gratis != null || $gratis != ''):
                        $diskon = "Beli ".$beli." Gratis ".$gratis;
                    else:
                        $diskon = "Tidak diskon";
                    endif;

                    $tampil = "<p style='font-size:0.8em;'><b style='color:##0b0259;font-size:1.4em;'>".$nama."</b>  - Satuan(".$satuan.")<br>".$diskon."</p>";
                    return $tampil;
                },
                'footer' => "<div style='text-transform:center;'>Jumlah</div><div style='text-transform:center;'>Diskon</div>",
            ],
            //'jenis:ntext',
            // 'satuan:ntext',
            // [
            //     'attribute' => 'diskon_jumlah_beli',
            //     'header' => 'Diskon',
            //     'format' => 'raw',
            //     'value' => function($model){
            //         $beli = $model->diskon_jumlah_beli;
            //         $gratis = $model->free_diskon;
            //         if($beli != null || $beli != '' && $gratis != null || $gratis != ''):
            //             return "Beli ".$beli." Gratis ".$gratis;
            //         else:
            //             return "Tidak diskon";
            //         endif;
            //     }
            // ],
            // 'harga_pokok:ntext',
            // echo Editable::widget([
            //     'model' => $model, 
            //     'attribute' => 'rating',
            //     'type' => 'primary',
            //     'size'=> 'lg',
            //     'inputType' => Editable::INPUT_RATING,
            //     'editableValueOptions' => ['class' => 'text-success h3']
            // ]);
            [
                'attribute' => 'harga_jual',
                'format' => 'raw',
                'value' => function($model){
                    //$total += $model->harga_jual;
                    return "<span style='font-size:0.8em;float:left;'>Rp</span> <span style='float:right;font-size:0.8em;'>".number_format($model->harga_jual,0,',','.').",00</span>";
                },
                'footer' => "<div style='text-align:left;'>Rp <span id='pjx-hasil-jual' style='text-align:right;float:rigt;font-size:1.0em;'>".$Tamount.",00</span></div><div style='text-align:left;'>Rp <span id='pjx-hasil-diskon' style='text-align:right;float:rigt;font-size:1.0em;'>".$Tdiskon.",00</span></div>",
            ],
            // 'deskripsi:ntext',
            // 'gambar:ntext',
            // [
            //     'class'=>'kartik\grid\EditableColumn',
            //     'width' => "100px",
            //     'attribute' => 'jumlah',
            //     'filter' => false,
            //     'editableOptions' => [
            //         'inputType'=>Editable::INPUT_TEXT,
            //         'asPopover' => false,
            //         'options'=>[
            //             'class'=>'form-control',
            //             'style' => 'width:100%;margin-left:2px;'
            //             //'pluginOptions'=>['min'=>1, 'max'=>4]
            //         ],
            //         'formOptions' => ['action' => 'edit-jumlah-keranjang'],
            //     ],
            // ],
            [
                'attribute' => 'jumlah',
                'filter' => false,
                'format' => 'raw',
                'value' => function($model){
                    Pjax::begin(['id'=>'tambah-kurang-'.$model->id, 'enablePushState' => false]);
                        $this->registerJs("
                            $('.jumlah-".$model->id."').html('".$model->jumlah."');
                        ");
                    Pjax::end();

                    $push = Html::a(Yii::t('app', '<span class="fa fa-plus-circle tambah-'.$model->id.'" id="tambah-jumlah"></span>'), ['update-jumlah','act'=>1,'idp'=>$model->id], [
                            //'id' => 'tambah-jumlah',
                            'onclick' => "
                                $.ajax({
                                    url: '".Url::to(['update-jumlah','act'=>'1','idp'=>$model->id])."',
                                    type: 'POST',
                                    success: function() {
                                        $.pjax.reload({container:'#pjx-keranjang-index', async:false});
                                        $.pjax.reload({container:'#tambah-kurang-".$model->id."', async:false});
                                    }
                                });
                                return false;
                            "
                        ]);
                    $queve =  Html::a(Yii::t('app', '<span class="fa fa-minus-circle kurang-'.$model->id.'" id="kurang-jumlah"></span>'), ['update-jumlah','act'=>0,'idp'=>$model->id], [
                            //'id' => 'tambah-jumlah',
                            'onclick' => "
                                $.ajax({
                                    url: '".Url::to(['update-jumlah','act'=>'0','idp'=>$model->id])."',
                                    type: 'POST',
                                    success: function() {
                                        $.pjax.reload({container:'#pjx-keranjang-index', async:false});
                                        $.pjax.reload({container:'#tambah-kurang-".$model->id."', async:false});
                                    }
                                });
                                return false;
                            "
                        ]);
                    return $queve."<span id='jumlah-keranjang' class='jumlah-".$model->id."'>".$model->jumlah."</span> ".$push;
                }
            ],
            // 'status:ntext',
            // 'no_transaksi:ntext',
            // 'nama_pelanggan:ntext',
            // 'no_telepon:ntext',
            // 'tanggal_expired:ntext',
            // 'diskon_jumlah_beli',
            // 'free_diskon',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'options' => ['style' => "width:90px;"],
                'template' => '{delete}',
                'buttons' => [
                    // 'view' => function ($url, $model) {
                    //         return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                    //             'title' => Yii::t('app', 'View'),
                    //             // 'class' => 'btn-ajax-modal',
                    //             // 'id' => 'activity-view-link',
                    //             // 'data-toggle' => 'modal',
                    //             // 'data-target' => '#myModal',
                    //     ]);
                    // },
                    'delete' => function ($url, $model) {
                            return Html::a('<span class="fa fa-close" style="font-size:1.3em;float:right;vertical-align:middle;margin-right:20px;"></span>', '#', [
                                'title' => Yii::t('app', 'Hapus Item'),
                                //'data-confirm' => 'Yakin ingin menghapus?',
                                'onclick' => "
                                    if(confirm('Yakin ingin menghapus item ?')){
                                        $.ajax({
                                            url: '".$url."',
                                            type: 'POST',
                                            success: function() {
                                                $.pjax.reload({container:'#pjx-keranjang', async:false});
                                                $.pjax.reload({container:'#keranjang-pesan', async:false});
                                                $.pjax.reload({container:'#count-keranjang', async:false});
                                            }
                                        });
                                        return false;
                                    }
                                "
                                // 'id' => 'activity-update-link',
                                // 'data-toggle' => 'modal',
                                // 'class' => 'btn-ajax-modal',
                                // 'data-target' => '#myModal',
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete'):
                        return Url::toRoute(['delete-keranjang', 'id' => $model->id]);
                    endif;
                }
            ],
        ],
        'id' => 'pjax-gridview-keranjang',
        'pjax' => 0,
        'export'=>false,
        //'bordered' => true,
        //'striped' => false,
        //'condensed' => false,
        //'responsive' => false,
        'responsiveWrap' => false,
        'hover' => false,
        //'floatHeader' => false,
        'showPageSummary' => false,
        //'floatHeaderOptions' => ['scrollingTop' => 5],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading'=>"Keranjang Pembelian",
            'footer' => false,
        ],
    ]); ?>

<?php Pjax::end(); ?>
    <div class="konfirmasi-form">
        <p style="text-transform:uppercase;text-align:center;">isi data pelanggan</p>
        <?php $form = ActiveForm::begin(['id'=>'id-konnfirmasi', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'nama_pelanggan')->textInput(['placeholder' => 'Nama Pelanggan (*)','maxLength'=>40])->label(false) ?>

            <?= $form->field($model, 'no_telepon')->textInput(['placeholder' => 'Nomor HP/Whatsapp (*)','maxLength'=>16])->label(false) ?>

            <?= $form->field($model, 'no_rek_pelanggan')->textInput(['placeholder' => 'No. Rekening Pelanggan (*)','maxLength'=>20])->label(false) ?>

            <?= $form->field($model, 'rek_a_n')->textInput(['placeholder' => 'Rekening Atas Nama (*)','maxLength'=>40])->label(false) ?>

            <?= $form->field($model, 'bank')->dropDownList([ 
                'BCA' => 'BCA',
                'BNI' => 'BNI',
                'BRI' => 'BRI',
                'DANAMON' => 'DANAMON',
                'Mandiri' => 'Mandiri',
            ], ['prompt' => 'Nama BANK (*)'])->label(false) ?>

            <fieldset>
                <legend>Alamat Pengiriman</legend>
                <?= $form->field($model, 'wilayah')->dropDownList(ArrayHelper::map($wilSQL->queryAll(),'id',
                        function($model, $defaultValue){
                            return $model['nama'];
                        }
                    ), [
                    'prompt' => 'Pilih Wilayah Pengiriman (*)', 'style'=>'width:100%;',
                ])->label(false) ?>

                <?= $form->field($model, 'alamat_lengkap')->textarea(['placeholder'=>'Alamat Lengkap Pengiriman (*)','rows' => 4,'style'=>'resize:none;'])->label(false) ?>
            </fieldset>
            <div class="form-group" style="clear:left;text-align:center;margin-top:10px;margin-bottom:-5px;">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Lanjut Pembayaran') : Yii::t('app', 'Lanjut Pembayaran'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','data-confirm'=>'Silahkan dilanjutkan jika data telah benar']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php else:?>
    <b><i>Tidak ada kerajang belanja</i></b>
<?php endif;?>
</div>