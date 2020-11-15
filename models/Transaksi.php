<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transaksi".
 *
 * @property integer $id
 * @property string $ip
 * @property string $nama_produk
 * @property string $jenis
 * @property string $satuan
 * @property string $harga_pokok
 * @property string $harga_jual
 * @property string $deskripsi
 * @property string $gambar
 * @property integer $jumlah
 * @property string $status
 * @property string $no_transaksi
 * @property string $nama_pelanggan
 * @property string $no_telepon
 * @property string $tanggal_expired
 * @property integer $diskon_jumlah_beli
 * @property integer $free_diskon
 */
class Transaksi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaksi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip', 'nama_produk', 'jenis', 'satuan', 'harga_pokok', 'harga_jual', 'status', 'tanggal_expired'], 'required'],
            [['ip', 'nama_produk', 'jenis', 'satuan', 'harga_pokok', 'harga_jual', 'deskripsi', 'status', 'no_transaksi', 'nama_pelanggan', 'no_telepon', 'tanggal_expired', 'no_resi', 'kurir'], 'string'],
            [['jumlah', 'diskon_jumlah_beli', 'free_diskon'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'IP'),
            'nama_produk' => Yii::t('app', 'Nama Produk'),
            'jenis' => Yii::t('app', 'Jenis'),
            'satuan' => Yii::t('app', 'Satuan'),
            'harga_pokok' => Yii::t('app', 'Harga Pokok'),
            'harga_jual' => Yii::t('app', 'Harga Jual'),
            'deskripsi' => Yii::t('app', 'Deskripsi'),
            'jumlah' => Yii::t('app', 'Jumlah'),
            'status' => Yii::t('app', 'Status'),
            'no_transaksi' => Yii::t('app', 'No Transaksi'),
            'nama_pelanggan' => Yii::t('app', 'Nama Pelanggan'),
            'no_telepon' => Yii::t('app', 'No Telepon'),
            'tanggal_expired' => Yii::t('app', 'Tanggal Expired'),
            'diskon_jumlah_beli' => Yii::t('app', 'Diskon Jumlah Beli'),
            'free_diskon' => Yii::t('app', 'Free Diskon'),
            'no_resi' => Yii::t('app', 'No Resi'),
            'kurir' => Yii::t('app', 'Jasa Pengiriman'),
        ];
    }
}
