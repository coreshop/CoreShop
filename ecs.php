<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\EregToPregFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer;
use PhpCsFixer\Fixer\Alias\PowToExponentiationFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer;
use PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTagTypeFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->rules([
        BinaryOperatorSpacesFixer::class,
        BlankLineAfterNamespaceFixer::class,
        BlankLineAfterOpeningTagFixer::class,
        BlankLineBeforeStatementFixer::class,
        CastSpacesFixer::class,
        ClassAttributesSeparationFixer::class,
        CombineConsecutiveIssetsFixer::class,
        CombineConsecutiveUnsetsFixer::class,
        DeclareEqualNormalizeFixer::class,
        DeclareStrictTypesFixer::class,
        DirConstantFixer::class,
        ElseifFixer::class,
        EncodingFixer::class,
        EregToPregFixer::class,
        ErrorSuppressionFixer::class,
        FullOpeningTagFixer::class,
        FunctionDeclarationFixer::class,
        FunctionToConstantFixer::class,
        FunctionTypehintSpaceFixer::class,
        GeneralPhpdocTagRenameFixer::class,
        IncludeFixer::class,
        IndentationTypeFixer::class,
        IsNullFixer::class,
        LineEndingFixer::class,
        LowercaseCastFixer::class,
        LowercaseKeywordsFixer::class,
        LowercaseStaticReferenceFixer::class,
        MagicConstantCasingFixer::class,
        MethodArgumentSpaceFixer::class,
        ModernizeTypesCastingFixer::class,
        NativeConstantInvocationFixer::class,
        NativeFunctionCasingFixer::class,
        NewWithBracesFixer::class,
        NoAliasFunctionsFixer::class,
        NoBlankLinesAfterClassOpeningFixer::class,
        NoBlankLinesAfterPhpdocFixer::class,
        NoBreakCommentFixer::class,
        NoClosingTagFixer::class,
        NoEmptyCommentFixer::class,
        NoEmptyPhpdocFixer::class,
        NoEmptyStatementFixer::class,
        NoHomoglyphNamesFixer::class,
        NoLeadingImportSlashFixer::class,
        NoLeadingNamespaceWhitespaceFixer::class,
        NoMultilineWhitespaceAroundDoubleArrowFixer::class,
        NonPrintableCharacterFixer::class,
        NoNullPropertyInitializationFixer::class,
        NoPhp4ConstructorFixer::class,
        NormalizeIndexBraceFixer::class,
        NoShortBoolCastFixer::class,
        NoSinglelineWhitespaceBeforeSemicolonsFixer::class,
        NoSpacesAfterFunctionNameFixer::class,
        NoSpacesAroundOffsetFixer::class,
        NoSpacesInsideParenthesisFixer::class,
        NoSuperfluousElseifFixer::class,
        NoTrailingCommaInListCallFixer::class,
        NoTrailingCommaInSinglelineArrayFixer::class,
        NoTrailingWhitespaceFixer::class,
        NoTrailingWhitespaceInCommentFixer::class,
        NoUnneededControlParenthesesFixer::class,
        NoUnneededCurlyBracesFixer::class,
        NoUnneededFinalMethodFixer::class,
        NoUnusedImportsFixer::class,
        NoUselessElseFixer::class,
        NoWhitespaceBeforeCommaInArrayFixer::class,
        NoWhitespaceInBlankLineFixer::class,
        ObjectOperatorWithoutWhitespaceFixer::class,
        OrderedImportsFixer::class,
        PhpdocIndentFixer::class,
        PhpdocInlineTagNormalizerFixer::class,
        PhpdocNoAccessFixer::class,
        PhpdocNoAliasTagFixer::class,
        PhpdocNoEmptyReturnFixer::class,
        PhpdocNoPackageFixer::class,
        PhpdocNoUselessInheritdocFixer::class,
        PhpdocReturnSelfReferenceFixer::class,
        PhpdocScalarFixer::class,
        PhpdocSeparationFixer::class,
        PhpdocSingleLineVarSpacingFixer::class,
        PhpdocTagTypeFixer::class,
        PhpdocTrimFixer::class,
        PhpdocTypesFixer::class,
        PhpdocVarWithoutNameFixer::class,
        PhpUnitDedicateAssertFixer::class,
        PhpUnitFqcnAnnotationFixer::class,
        PowToExponentiationFixer::class,
        ProtectedToPrivateFixer::class,
        ReturnTypeDeclarationFixer::class,
        SelfAccessorFixer::class,
        ShortScalarCastFixer::class,
        SingleBlankLineAtEofFixer::class,
        SingleBlankLineBeforeNamespaceFixer::class,
        SingleClassElementPerStatementFixer::class,
        SingleImportPerStatementFixer::class,
        SingleLineAfterImportsFixer::class,
        SingleQuoteFixer::class,
        SpaceAfterSemicolonFixer::class,
        StandardizeNotEqualsFixer::class,
        SwitchCaseSemicolonToColonFixer::class,
        SwitchCaseSpaceFixer::class,
        TernaryOperatorSpacesFixer::class,
        TernaryToNullCoalescingFixer::class,
        TrimArraySpacesFixer::class,
        UnaryOperatorSpacesFixer::class,
        WhitespaceAfterCommaInArrayFixer::class,
        \Symplify\CodingStandard\Fixer\Spacing\StandaloneLineConstructorParamFixer::class
    ]);

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, ['syntax' => 'short']);
    $ecsConfig->ruleWithConfiguration(BracesFixer::class, ['allow_single_line_closure' => true]);
    $ecsConfig->ruleWithConfiguration(ClassDefinitionFixer::class, ['single_item_single_line' => true, 'multi_line_extends_each_single_line' => true]);
    $ecsConfig->ruleWithConfiguration(ConcatSpaceFixer::class, ['spacing' => 'one']);
    $ecsConfig->ruleWithConfiguration(ConstantCaseFixer::class, ['case' => 'lower']);
    $ecsConfig->ruleWithConfiguration(IncrementStyleFixer::class, ['style' => 'pre']);
    $ecsConfig->ruleWithConfiguration(ListSyntaxFixer::class, ['syntax' => 'short']);
    $ecsConfig->ruleWithConfiguration(MultilineWhitespaceBeforeSemicolonsFixer::class, ['strategy' => 'new_line_for_chained_calls']);
    $ecsConfig->ruleWithConfiguration(NoExtraBlankLinesFixer::class, ['tokens' => ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use']]);
    $ecsConfig->ruleWithConfiguration(NoMixedEchoPrintFixer::class, ['use' => 'echo']);
    $ecsConfig->ruleWithConfiguration(NoSuperfluousPhpdocTagsFixer::class, ['allow_mixed' => true]);
    $ecsConfig->ruleWithConfiguration(OperatorLinebreakFixer::class, ['only_booleans' => true, 'position' => 'end']);
    $ecsConfig->ruleWithConfiguration(PhpdocTypesOrderFixer::class, ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']);
    $ecsConfig->ruleWithConfiguration(SingleLineCommentStyleFixer::class, ['comment_types' => ['hash']]);
    $ecsConfig->ruleWithConfiguration(TrailingCommaInMultilineFixer::class, ['elements' => ['arrays', 'arguments', 'parameters']]);
    $ecsConfig->ruleWithConfiguration(VisibilityRequiredFixer::class, ['elements' => ['const', 'property', 'method']]);

    $header = <<<EOT
CoreShop

This source file is available under two different licenses:
 - GNU General Public License version 3 (GPLv3)
 - CoreShop Commercial License (CCL)
Full copyright and license information is available in
LICENSE.md which is distributed with this source code.

@copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
@license    https://www.coreshop.com/license     GPLv3 and CCL
 
EOT;

    $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, ['header' => $header]);
};
