# TemplateType

The [TemplateType](../../../src/Column/Type/TemplateType.php) represents a column with value displayed as a link.

## Options

### `template_path`

**type**: `string` or `callable`

Sets the path to the template that should be rendered.  
Callable can be used to provide an option value based on a row value, which is passed as a first argument.

### `template_vars`

**type**: `string` or `callable` **default**: `'#'`

Sets the variables used within the template.  
Callable can be used to provide an option value based on a row value, which is passed as a first argument.

## Inherited options

See [abstract column type documentation](abstract.md).