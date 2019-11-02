<?php

namespace UniteCMS\CoreBundle\Field;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UniteCMS\CoreBundle\Content\FieldData;
use UniteCMS\CoreBundle\Content\ContentInterface;
use UniteCMS\CoreBundle\ContentType\ContentType;
use UniteCMS\CoreBundle\ContentType\ContentTypeField;
use UniteCMS\CoreBundle\Query\QueryComparison;
use UniteCMS\CoreBundle\Query\QueryOrderBy;

interface FieldTypeInterface
{
    static function getType(): string;

    public function GraphQLInputType(ContentTypeField $field) : ?string;

    public function validateFieldDefinition(ContentType $contentType, ContentTypeField $field, ExecutionContextInterface $context) : void;

    public function resolveField(ContentInterface $content, ContentTypeField $field, FieldData $fieldData);

    public function normalizeInputData(ContentInterface $content, ContentTypeField $field, $inputData = null) : FieldData;

    public function validateFieldData(ContentInterface $content, ContentTypeField $field, ExecutionContextInterface $context, FieldData $fieldData = null) : void;

    public function queryOrderBy(ContentTypeField $field, array $sortInput) : ?QueryOrderBy;

    public function queryComparison(ContentTypeField $field, array $whereInput) : ?QueryComparison;
}
