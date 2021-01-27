<?php

namespace common\models;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * This is the model class for table "course".
 *
 * @property int $id
 * @property string $name
 * @property string $desc
 * @property int $category_id
 * @property int $direction_id
 * @property int $category_education
 * @property CourseCategory $category
 * @property DirectionCourse $direction
 * @property string $received_document [varchar(400)]
 * @property string $seo_url [varchar(1000)]
 * @property string $seo_desc [varchar(1000)]
 * @property string $seo_name [varchar(1000)]
 * @property string $for_whom [varchar(1000)]
 * @property string $program
 * @property-read null|string $directory
 * @property-read ActiveQuery $webinarForm
 * @property-read ActiveQuery $fullTimeForm
 * @property-read ActiveQuery $onlineForm
 * @property-read string $path
 * @property-read string $pathLink
 * @property-read ActiveQuery $nmoData
 * @property-read ActiveQuery $remoteForm
 */
class Course extends ActiveRecord
{
    public UploadedFile $imageMain;
    public UploadedFile $imagePreview;
    public ?int $specialization_id;
    public ?string $point = '';
    public ?int $category_education;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'seo_url',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['specialization_id'], 'safe'],
            [['imageMain', 'imagePreview',], 'file', 'maxFiles' => 1000],
            [['desc', 'program'], 'string'],
            [['category_id'], 'required'],
            [['category_id', 'direction_id', 'point', 'category_education'], 'integer'],
            [['received_document'], 'string', 'max' => 400],
            [['name', 'seo_url'], 'string', 'max' => 1000],
            [['category_id'], 'exist', 'skipOnError' => true,
                'targetClass' => CourseCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DirectionCourse::class, 'targetAttribute' => ['direction_id' => 'id']],
            [['for_whom'], 'string', 'max' => 1000],
            [['category_education'], 'default', 'value' => null]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'desc' => 'Описание',
            'category_id' => 'Категория',
            'direction_id' => 'Направление',
            'imageMain' => 'Картинка обложка',
            'imagePreview' => 'Картинка превью',
            'specialization_id' => 'Специальности',
            'point' => 'Кол-во баллов/часов',
            'received_document' => 'Получаемый документ',
            'seo_url' => 'ссылка на курс',
            'category_education' => 'Категория обучения',
            'for_whom' => 'Для кого',
            'program' => 'Программа',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->category_id == 1) {
            $NmoData = NmoData::findOne(['id_course' => $this->id]);
            if (empty($NmoData)) {
                $NmoData = new NmoData();
                $NmoData->id_course = $this->id;
            }
            $NmoData->specialization_id = Json::encode($this->specialization_id);
            $NmoData->point = $this->point;
            $NmoData->category_education = $this->category_education;
            $NmoData->save();
        } else {
            $NmoData = NmoData::findOne(['id_course' => $this->id]);
            if (!empty($NmoData)) {
                try {
                    $NmoData->delete();
                } catch (StaleObjectException $e) {
                    $e->getMessage();
                } catch (Throwable $e) {
                    $e->getMessage();
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function getDirectory()
    {
        $directoryFile = Yii::getAlias('@storage') . '/course/' . $this->id;
        if (!is_dir($directoryFile)) try {
            FileHelper::createDirectory($directoryFile);
        } catch (Exception $e) {
            return null;
        }
        return $directoryFile;
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CourseCategory::class, ['id' => 'category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(DirectionCourse::class, ['id' => 'direction_id']);
    }

    public function getImagePreview()
    {
        $files = $this->findFile();
        if (!empty($files) && $this->id > 0) {
            foreach ($files as $file) {
                if (strpos($file, 'preview') !== false) {
                    return $this->getPathLink() . '/' . basename($file);
                }
            }
        }
        return null;
    }

    public function getImageMain()
    {
        $files = $this->findFile();
        if (!empty($files) && $this->id > 0) {
            foreach ($files as $file) {
                if (strpos($file, 'main') !== false) {
                    return $this->getPathLink() . '/' . basename($file);
                }
            }
        }

        return null;
    }


    public function findFile()
    {
        if (file_exists($this->getPath())) {
            return yii\helpers\FileHelper::findFiles($this->getPath());
        }
        return null;
    }

    public function getPath()
    {
        return Yii::getAlias('@storage') . '/course/' . $this->id;
    }

    public function getPathLink()
    {
        return Yii::getAlias('@storageUrl') . '/course/' . $this->id;
    }

    /**
     * @return ActiveQuery
     */
    public function getNmoData()
    {
        return $this->hasOne(NmoData::class, ['id_course' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRemoteForm()
    {
        return $this->hasOne(RemoteForm::class, ['id' => 'id_course']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFullTimeForm()
    {
        return $this->hasOne(FullTimeForm::class, ['id' => 'id_course']);
    }


    /**
     * @return ActiveQuery
     */
    public function getWebinarForm()
    {
        return $this->hasOne(WebinarForm::class, ['id' => 'id_course']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOnlineForm()
    {
        return $this->hasOne(OnlineForm::class, ['id' => 'id_course']);
    }

    public function upload()
    {
        $directoryFile = $this->getDirectory();
        if (!empty($this->imageMain) && !empty($directoryFile)) {
            $fileName = $directoryFile . '/main.' . $this->imageMain->extension;
            unlink($fileName);
            $this->imageMain->saveAs($fileName);
        }

        if (!empty($this->imagePreview) && !empty($directoryFile)) {
            $fileName = $directoryFile . '/preview.' . $this->imagePreview->extension;
            unlink($fileName);
            $this->imagePreview->saveAs($fileName);
        }
    }

    public static function getInfo()
    {
        return [
            'remote' => ['desc' => 'Описание дистанционных курсов',
                'name' => 'Дистанционные курсы', 'url' => 'remote', 'tagName' => 'Дистанционный курс', 'hourName' => ' ч'],
            'webinar' => ['desc' => 'Описание вебинаров',
                'name' => 'Вебинар', 'url' => 'webinar', 'tagName' => 'Вебинары', 'hourName' => ' мин'],
            'online' => ['desc' => 'Описание онлайн курсов',
                'name' => 'Онлайн-курс', 'url' => 'online', 'tagName' => 'Онлайн курс', 'hourName' => ' ч'],
            'full-time' => ['desc' => 'Описание очных курсов',
                'name' => 'Очное обучение', 'url' => 'full-time', 'tagName' => 'Очный', 'hourName' => ' ч'],
        ];
    }
}
