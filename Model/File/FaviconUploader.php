<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\Model\File;

use Magento\Framework\Filesystem;
use Magento\Framework\Validation\ValidationException;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;

/**
 * Uploader du seul champ « favicon de l administration ».
 *
 * POURQUOI. L uploader du coeur valide toute image reconnue en demandant a
 * l adaptateur configure de l OUVRIR. Pour un ICO, l adaptateur par defaut
 * (GD) leve « Unsupported image format », et le televersement echoue sur
 * « File validation failed. Check Image Processing Settings in the Store
 * Configuration. » Aucun ICO ne passe donc, celui du favicon de la boutique
 * pas davantage.
 *
 * Contourner ce controle GLOBALEMENT — en changeant l adaptateur d image du
 * site pour ImageMagick, ou en remplacant le validateur du coeur par une
 * preference — reglerait le symptome au prix du reste : toutes les images du
 * site changeraient de chemin de validation. On branche donc cet uploader sur
 * ce seul champ, par un type virtuel de fabrique (etc/di.xml).
 *
 * Ce que nous conservons du coeur : extension autorisee, extension non
 * protegee, taille maximale, et le controle d image complet pour tout format
 * autre que l ICO. Ce que nous remplacons : pour un ICO, l ouverture par GD
 * cede la place a une lecture de l ICONDIR (voir IcoValidator).
 */
class FaviconUploader extends Uploader
{
    private IcoValidator $icoValidator;

    /**
     * @param string|array $fileId
     *
     * Le validateur n est PAS optionnel a dessein : Magento ne remplit pas les
     * parametres de constructeur optionnels, ils recoivent null.
     */
    public function __construct(
        $fileId,
        Database $coreFileStorageDb,
        Storage $coreFileStorage,
        NotProtectedExtension $validator,
        IcoValidator $icoValidator,
        ?Filesystem $filesystem = null
    ) {
        $this->icoValidator = $icoValidator;
        parent::__construct($fileId, $coreFileStorageDb, $coreFileStorage, $validator, $filesystem);
    }

    /**
     * @return void
     * @throws ValidationException
     */
    protected function _validateFile()
    {
        if (strtolower($this->getFileExtension()) !== 'ico') {
            parent::_validateFile();

            return;
        }

        // L aieul : extension autorisee et rappels de validation (dont la
        // taille maximale), sans le controle d image de Magento\MediaStorage.
        \Magento\Framework\File\Uploader::_validateFile();

        if ($this->_fileExists === false) {
            return;
        }

        if (!$this->icoValidator->isValid((string) $this->_file['tmp_name'])) {
            throw new ValidationException(
                __('This file is not a valid icon. Upload an ICO, PNG, GIF or JPG file.')
            );
        }
    }
}
