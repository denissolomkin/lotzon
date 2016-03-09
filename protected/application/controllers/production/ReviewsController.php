<?php
namespace controllers\production;
use \Application, \WideImage, \Player, \EntityException, \ReviewsModel, \Review, \ModelException;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ReviewsController extends \AjaxController
{
    public function init()
    {
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
            if (!$this->session->get(Player::IDENTITY) instanceof Player) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
            $this->session->get(Player::IDENTITY)->markOnline();
        }
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $playerId = $this->session->get(Player::IDENTITY)->getId();
            $text = $this->request()->post('text');
            $reviewId = $this->request()->post('reviewId');
            $image = $this->request()->post('image');

            $reviewObj = new Review;
            $reviewObj->setPlayerId($playerId)
                ->setText($text)
                ->setReviewId($reviewId)
                ->setImage($image);

            try {
                $reviewObj->create();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }

    public function removeImageAction()
    {

        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $image = $this->request()->post('image');

            if(ReviewsModel::instance()->imageExists($image))
                $response = array(
                'status'  => 0,
                'message' => 'Image Links To Review',
            );
            else
            try {

                @unlink(PATH_FILESTORAGE . 'reviews/' . $image);
            } catch (Exception $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }


    public function uploadImageAction()
    {
        try {

            $image = WideImage::loadFromUpload('image');
            $image = $image->resizeDown(Review::IMAGE_WIDTH, null, 'fill');
            $image = $image->resizeDown(null, Review::IMAGE_HEIGHT, 'outside');
            //$image = $image->crop("center", "center", Review::IMAGE_WIDTH, Review::IMAGE_HEIGHT);

            $imageName = ($this->request()->post('Image'))?:uniqid() . ".jpg";
            $imageName = uniqid() . ".jpg";
            $saveFolder = PATH_FILESTORAGE . 'reviews/';

            if (!is_dir($saveFolder)) {
                mkdir($saveFolder, 0777);
            }

            $image->saveToFile($saveFolder . $imageName, 100);

            \Common::saveImageMultiResolution('', PATH_FILESTORAGE . 'reviews/', $imageName, array(array(600)), $saveFolder . $imageName);

            $data = array(
                'imageName' => $imageName,
                'imageWebPath' => '/filestorage/reviews/' . $imageName,
            );
            $this->ajaxResponse($data);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }
    }

}