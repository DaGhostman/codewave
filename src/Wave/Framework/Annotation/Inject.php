<?php
/**
 * Created by PhpStorm.
 * User: elham_asmar
 * Date: 01/04/2015
 * Time: 11:40
 */

namespace Wave\Framework\Annotation;

/**
 * Class Inject
 *
 * @package Wave\Framework\Annotation
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("type", type = "string", required = true),
 *   @Attribute("name", type = "string", required = true),
 * })
 */
class Inject {
    /**
     * @Enum({"Object", "Static"})
     */
    public $type;
    public $name;
}