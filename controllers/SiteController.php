<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SearchPForm;
use app\models\ContactForm;
use app\models\User;
use app\models\Produk;
use app\models\ProdukSearch;
use app\models\ProdukGambar;
use app\models\ProdukGambarSearch;
use app\models\Transaksi;
use app\models\TransaksiSearch;
use app\models\Konfirmasi;
use app\models\KonfirmasiSearch;
use app\models\CekPemesanan;
use yii\db\Query;
use yii\db\Expression;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\UploadedFile;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionBantuan()
    {
        return $this->render('bantuan');
    }

    public function actionDeleteKeranjang($id)
    {
        if (!Yii::$app->user->isGuest):
            Yii::$app->user->logout();
            return $this->goHome();
        endif;

        $ip = Yii::$app->getRequest()->getUserIP();
        $model = Transaksi::find()->where("id='$id' AND ip='$ip'")->one();
        $connection = \Yii::$app->db;
        $query = $connection->createCommand("DELETE FROM transaksi WHERE id='$id' AND ip='$ip'")->execute();
    }

    public function actionViewProduk($id)
    {
        if (!Yii::$app->user->isGuest):
            Yii::$app->user->logout();
            return $this->goHome();
        endif;

        return $this->render('view-produk',[
            'model' => $model=Produk::findOne($id),
            'gambar' => ProdukGambar::find()->where("produk='$model->id'")->all(),
        ]);
    }

    public function actionSideKategori($id)
    {
        if (!Yii::$app->user->isGuest):
            Yii::$app->user->logout();
            return $this->goHome();
        endif;

        $model2 = new SearchPForm();

        $query = Produk::find()->where("jenis='$id'");
        if($model2->load(Yii::$app->request->post())):
            $search = $_POST['SearchPForm']['search'];

            $pagination = new Pagination([
                'defaultPageSize' => 12,
                'totalCount' => $query->count(),
            ]);
            if($search != '' || $search != NULL):
                $model = $query->orderBy('nama ASC')
                    ->andFilterWhere(['like', 'nama', $search])
                    ->andFilterWhere(['jenis'=> $id])
                    ->andFilterWhere(['like', 'deskripsi', $search])
                    //->offset($pagination->offset)
                    ->all();
                if($model == NULL):
                    Yii::$app->session->setFlash('danger', "<span class='fa fa-close'></span> Data tidak ditemukan.");
                    return $this->refresh();
                endif;
            else:
                return $this->refresh();
            endif;
        else:
            $pagination = new Pagination([
                'defaultPageSize' => 12,
                'totalCount' => $query->count(),
            ]);
            $model = $query->orderBy('nama ASC')
                    ->andFilterWhere(['jenis'=> $id])
                    //->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();
        endif;

        return $this->render('index',[
            'model' => $model,
            'pagination' => $pagination,
            'model2' => $model2
        ]);
    }

    public function actionUpdateJumlah()
    {
     
        $act=$_GET['act'];
        $id = $_GET['idp'];
        $model = Transaksi::findOne($id);
        $temp = $model->jumlah;
        if($act == "1"):
            $model->jumlah = $temp+1;
            $model->save(false);
        elseif($act == "0"):
            if($model->jumlah > 1):
                $model->jumlah = $temp-1;
                $model->save(false);
            endif;
        endif;   
    }

    public function actionEditJumlahKeranjang()
    {
        if (Yii::$app->request->post('hasEditable')) :
            $model = Transaksi::findOne($_POST['editableKey']);
     
            $out = Json::encode(['output'=>'', 'message'=>'']);
     
            $post = [];
            $posted = current($_POST['Transaksi']);
            $post['Transaksi'] = $posted;
     
            if ($model->load($post) && $posted['jumlah'] != 0):
                $model->save(false);

                $output = '';
     
                if (isset($posted['jumlah'])) :
                   $output =  $model->jumlah;
                endif;

                $out = Json::encode(['output'=>$output, 'message'=>'']);
            else:
                $out = $model->jumlah;
                $out = Json::encode(['output'=>$output, 'message'=>'']);
            endif; 

            echo $out;
            return;
        endif;
    }

    public function actionCekPemesanan()
    {
        $model = new CekPemesanan();
        $ip = Yii::$app->getRequest()->getUserIP();
        $connection = \Yii::$app->db;

        if ($model->load(Yii::$app->request->post())):
            $search = $_POST['CekPemesanan']['search'];
            $cekSQL = $connection->createCommand("
                SELECT * FROM transaksi 
                WHERE ip='$ip' AND no_transaksi='$search'
                OR id NOT LIKE '$ip' AND no_transaksi='$search' AND status NOT LIKE 'Booking1'
            ")->queryAll();
            if($cekSQL != NULL):
                $cek2SQL = $connection->createCommand("
                    SELECT * FROM konfirmasi WHERE ip='$ip' AND no_transaksi='$search'
                    OR ip NOT LIKE '$ip' AND no_transaksi='$search' AND status NOT LIKE 'Booking1'
                ")->queryOne();
                return $this->redirect(['lanjut-pembayaran','id'=>$cek2SQL['id']]);
            else:
                Yii::$app->session->setFlash('danger', "<span class='fa fa-close'></span> Data tidak ditemukan.");
                return $this->redirect(['cek-pemesanan']);
            endif;
        endif;        
        return $this->render('cek-pemesanan',[
            'model' => $model,
            'ip' => $ip
        ]);
    }

    public function actionLanjutPembayaran($id)
    {
        $model = Konfirmasi::findOne($id);
        $ip = Yii::$app->getRequest()->getUserIP();
        $connection = \Yii::$app->db;

        $cek2SQL = $connection->createCommand("
            SELECT * FROM konfirmasi 
            WHERE no_transaksi='$model->no_transaksi' AND struk_bukti IS NOT NULL AND status='Diproses' 
            OR no_transaksi='$model->no_transaksi' AND struk_bukti IS NOT NULL AND status='Verifikasi' 
            OR no_transaksi='$model->no_transaksi' AND struk_bukti IS NOT NULL AND status='Pengantaran' 
            OR no_transaksi='$model->no_transaksi' AND struk_bukti IS NOT NULL AND status='Selesai'
        ")->queryOne();
        if($cek2SQL):
            return $this->redirect(['status-antar','trx'=>$model->no_transaksi]);
        else:
            if ($model->load(Yii::$app->request->post())):
                $model->status="Diproses";
                $gambar = UploadedFile::getInstance($model,'struk_bukti');

                $imageName = $model->no_transaksi;
                $gambar->saveAs('gambar/bukti-bayar/'.$imageName.'.'.$gambar->extension);
                $model->struk_bukti = $imageName.'.'.$gambar->extension;
                if($model->save()):
                    $sql = $connection->createCommand("UPDATE transaksi SET ip='$ip',tanggal_expired='$model->tanggal_expired',status='Diproses',no_transaksi='$model->no_transaksi',nama_pelanggan='$model->nama_pelanggan',no_telepon='$model->no_telepon' WHERE status='Booking2'");
                    $sql->execute();
                    return $this->redirect(['status-antar','trx'=>$model->no_transaksi]);
                endif;
            endif;
            return $this->render('detail-pembayaran',[
                'model' => $model
            ]);
        endif;
    }

    public function actionStatusAntar($trx)
    {
        $model = Konfirmasi::findOne(['no_transaksi'=>$trx]);
        $ip = Yii::$app->getRequest()->getUserIP();
        $connection = \Yii::$app->db;
        $cek2SQL = $connection->createCommand("SELECT * FROM konfirmasi WHERE no_transaksi='$model->no_transaksi' AND struk_bukti IS NOT NULL")->queryOne();
        
        if($cek2SQL):
            return $this->render('status-antar',[
                'model' => $model
            ]);
        else:
            return $this->redirect(['index']);
        endif;
    }

    public function actionLihatKeranjang()
    {
        $ip = Yii::$app->getRequest()->getUserIP();

        $model = new Konfirmasi();

        if ($model->load(Yii::$app->request->post())):
            $model->ip = $ip;
            $code = "RJ".date('yhdism');
            $model->no_transaksi = $code;
            $model->struk_bukti = null;
            $model->status = "Booking2";
            $model->tanggal_pesan = date('Y-m-d H:i:s');
            $model->tanggal_expired = date("Y-m-d H:i:s", strtotime("now")+7200); //expired 2 jam berikut
            if($model->save()):
                $connection = \Yii::$app->db;
                $sql = $connection->createCommand("UPDATE transaksi SET tanggal_expired='$model->tanggal_expired',status='Booking2',no_transaksi='$model->no_transaksi',nama_pelanggan='$model->nama_pelanggan',no_telepon='$model->no_telepon' WHERE ip='$ip' AND status='Booking1'");   
                $sql->execute();
                return $this->redirect(['lanjut-pembayaran','id'=>$model->id]);
            endif;
        else:
            $searchModel = new TransaksiSearch();
            $dataProvider = $searchModel->searchKeranjang(Yii::$app->request->queryParams, $ip);
            return $this->render('lihat-keranjang',[
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'ip' => $ip,
                'model' => $model
            ]);
        endif;
    }

    public function actionTambahKeranjang()
    {
        $id=$_GET['id'];
        $ip = Yii::$app->getRequest()->getUserIP();
        $produk = Produk::findOne($id);
        $model = new Transaksi();

        $model->ip = $ip;
        $model->nama_produk = $produk->nama;
        $model->jenis = $produk->jenis0->jenis;
        $model->satuan = $produk->satuan0->satuan;
        $model->harga_pokok = $produk->harga_pokok;
        $model->harga_jual = $produk->harga_jual;
        $model->deskripsi = $produk->deskripsi;
        $model->status = 'Booking1';

        $cek_produk = Transaksi::find()->where("ip='$ip' AND nama_produk='$produk->nama' AND jenis='$model->jenis' AND satuan='$model->satuan' AND status='Booking1'")->one();
        if($cek_produk == NULL):
            $model->jumlah = '1';
            $model->diskon_jumlah_beli = $produk->diskon_jumlah_beli;
            $model->free_diskon = $produk->free_diskon;
            $model->tanggal_expired = date("Y-m-d H:i:s", strtotime("now")+18000); //expired 5 jam berikut
            $model->save(false);
        else:
            $date = date("Y-m-d H:i:s", strtotime("now")+36000);
            $connection = \Yii::$app->db;
            $sql = $connection->createCommand("UPDATE transaksi SET tanggal_expired='$date' WHERE ip='$ip'");   
            $sql->execute();
            $cek_produk->jumlah += '1';
            $cek_produk->save(false);
        endif;
    }

    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest):
            Yii::$app->user->logout();
            return $this->goHome();
        endif;

        //$connection = \Yii::$app->db;

        //$model = $connection->createCommand("SELECT * FROM produk WHERE aktivasi='Aktif' ORDER BY new Expression('rand()')");
        //$model = Produk::find()->where("aktivasi='Aktif'")->orderby('tanggal_input')->all();

        $model2 = new SearchPForm();

        if($model2->load(Yii::$app->request->post())):
            $search = $_POST['SearchPForm']['search'];
            $query = Produk::find();

            $pagination = new Pagination([
                'defaultPageSize' => 12,
                'totalCount' => $query->count(),
            ]);
            if($search != '' || $search != NULL):
                $model = $query->orderBy('nama ASC')
                    ->andFilterWhere(['like', 'nama', $search])
                    ->orFilterWhere(['like', 'deskripsi', $search])
                    //->offset($pagination->offset)
                    ->all();
                if($model == NULL):
                    Yii::$app->session->setFlash('danger', "<span class='fa fa-close'></span> Data tidak ditemukan.");
                    return $this->refresh();
                endif;
            else:
                return $this->refresh();
            endif;
        else:
            $query = Produk::find();
            $pagination = new Pagination([
                'defaultPageSize' => 12,
                'totalCount' => $query->count(),
            ]);
            $model = $query->orderBy('nama ASC')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        endif;

        return $this->render('index',[
            'model' => $model,
            'pagination' => $pagination,
            'model2' => $model2
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLoginAdm()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()):
            $user = User::findOne(['username'=>$model->username]);
            if($user->level=='admin'):
                return $this->redirect(['/admin/index-admin']);
            elseif($user->level=='operator'):
                return $this->redirect(['/operator/index-operator']);
            else:
                return $this->goHome();
            endif;
        else:
            return $this->render('login', [
                'model' => $model,
            ]);
        endif;
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
