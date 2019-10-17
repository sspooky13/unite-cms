<?php


namespace UniteCMS\CoreBundle\ContentType;

use UniteCMS\CoreBundle\Field\FieldTypeManager;
use UniteCMS\CoreBundle\GraphQL\Util;
use UniteCMS\CoreBundle\Security\Voter\ContentVoter;
use GraphQL\Type\Definition\ObjectType;
use Symfony\Component\ExpressionLanguage\Expression;
use UniteCMS\CoreBundle\GraphQL\SchemaManager;
use Symfony\Component\Validator\Constraints as Assert;
use UniteCMS\CoreBundle\Validator\Constraints as UniteAssert;

class ContentType
{
    /**
     * @Assert\NotBlank
     * @Assert\Regex(SchemaManager::GRAPHQL_NAME_REGEX)
     * @var string
     */
    protected $id;

    /**
     * @var ContentTypeField[] $fields
     * @Assert\Valid
     * @UniteAssert\ContentTypeField
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $permissions;

    public function __construct(string $id)
    {
        $this->permissions = [
            ContentVoter::QUERY => 'true',
            ContentVoter::MUTATION => 'is_granted("ROLE_ADMIN")',
            ContentVoter::CREATE => 'is_granted("ROLE_ADMIN")',
            ContentVoter::READ => 'true',
            ContentVoter::UPDATE => 'is_granted("ROLE_ADMIN")',
            ContentVoter::DELETE => 'is_granted("ROLE_ADMIN")',
        ];
        $this->id = $id;
    }

    /**
     * @param ObjectType $type
     * @return static
     */
    static function fromObjectType(ObjectType $type) : self {
        $contentType = new static($type->name);

        if($args = Util::directiveArgs($type->astNode, 'access')) {
           $contentType->setPermissions($args);
        }

        foreach($type->getFields() as $field) {
            if($contentTypeField = ContentTypeField::fromFieldDefinition($field)) {
                $contentType->registerField($contentTypeField);
            }
        }

        return $contentType;
    }

    /**
     * @return string
     */
    public function printResultType() : string {
        return sprintf('type %1$sResult implements UniteContentResult {
            total: Int!
            result: [%1$s!]
        }', $this->getId());
    }

    /**
     * @param FieldTypeManager $fieldTypeManager
     * @return string
     */
    public function printInputType(FieldTypeManager $fieldTypeManager) : string {
        $inputFields = '';

        foreach($this->getFields() as $field) {
            $inputFields .= $field->printInputType($fieldTypeManager) . "\n";
        }

        return $inputFields ? sprintf('input %sInput { %s }', $this->getId(), $inputFields) : '';
    }

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @param ContentTypeField $field
     *
     * @return $this
     */
    public function registerField(ContentTypeField $field) : self {
        $this->fields[$field->getId()] = $field;
        return $this;
    }

    /**
     * @return ContentTypeField[]
     */
    public function getFields() : array {
        return $this->fields;
    }

    /**
     * @param string $id
     * @return ContentTypeField|null
     */
    public function getField(string $id) : ?ContentTypeField {
        return $this->fields[$id] ?? null;
    }

    /**
     * @param array $permissions
     * @return self
     */
    public function setPermissions(array $permissions) : self {
        $this->permissions = array_merge($this->permissions, $permissions);
        return $this;
    }

    /**
     * @param string $permission
     * @return Expression
     */
    public function getPermission(string $permission) : Expression {
        return new Expression($this->permissions[$permission] ?? 'false');
    }
}