<?php

namespace Emma\DI\Attribute;

use Attribute;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 * use "Injectable" attribute to mark a class as injectable
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Injectable
{

}