<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Nikita Pavlovskiy <nikitades@pm.me>
 */
final class PhpdocOrderThrowsAtTheEndFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'Lala/phpdoc_order_throws_at_the_end_fixer';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Annotations in PHPDoc should be ordered so that `@param` annotations come first, then `@throws` annotations, then `@return` annotations.',
            [
                new CodeSample(
                    '<?php
/**
 * Hello there!
 *
 * @throws Exception|RuntimeException foo
 * @custom Test!
 * @return int  Return the number of changes.
 * @param string $foo
 * @param bool   $bar Bar
 */
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer, PhpdocSeparationFixer, PhpdocTrimFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocIndentFixer, PhpdocNoEmptyReturnFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority(): int
    {
        return -2;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            $content = $token->getContent();
            // move param to start, return to end, leave throws in the middle
            $content = $this->moveParamAnnotations($content);
            // we're parsing the content again to make sure the internal
            // state of the dockblock is correct after the modifications
            $content = $this->moveReturnAnnotations($content);
            // move throws to the end
            $content = $this->moveThrowsAnnotations($content);
            // persist the content at the end
            $tokens[$index] = new Token([T_DOC_COMMENT, $content]);
        }
    }

    /**
     * Move all param annotations in before throws and return annotations.
     *
     * @param string $content
     *
     * @return string
     */
    private function moveParamAnnotations($content)
    {
        $doc = new DocBlock($content);
        $params = $doc->getAnnotationsOfType('param');

        // nothing to do if there are no param annotations
        if (empty($params)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType(['throws', 'return']);

        if (empty($others)) {
            return $content;
        }

        // get the index of the final line of the final param annotation
        $end = end($params)->getEnd();

        $line = $doc->getLine($end);

        // move stuff about if required
        foreach ($others as $other) {
            if ($other->getStart() < $end) {
                // we're doing this to maintain the original line indexes
                $line->setContent($line->getContent() . $other->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }

    /**
     * Move all return annotations after param annotations.
     *
     * @param string $content
     *
     * @return string
     */
    private function moveReturnAnnotations($content)
    {
        $doc = new DocBlock($content);
        $returns = $doc->getAnnotationsOfType('return');

        // nothing to do if there are no return annotations
        if (empty($returns)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType(['param']);

        // nothing to do if there are no other annotations
        if (empty($others)) {
            return $content;
        }

        // get the index of the first line of the first return annotation
        $start = $returns[0]->getStart();
        $line = $doc->getLine($start);

        // move stuff about if required
        foreach (array_reverse($others) as $other) {
            if ($other->getEnd() > $start) {
                // we're doing this to maintain the original line indexes
                $line->setContent($other->getContent() . $line->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }

    /**
     * Move all throws annotations after param and return annotations.
     *
     * @param string $content
     *
     * @return string
     */
    private function moveThrowsAnnotations($content)
    {
        $doc = new DocBlock($content);
        $throws = $doc->getAnnotationsOfType('throws');

        // nothing to do if there are no return annotations
        if (empty($throws)) {
            return $content;
        }

        $others = $doc->getAnnotationsOfType(['param', 'return']);

        // nothing to do if there are no other annotations
        if (empty($others)) {
            return $content;
        }

        // get the index of the first line of the first return annotation
        $start = $throws[0]->getStart();
        $line = $doc->getLine($start);

        // move stuff about if required
        foreach (array_reverse($others) as $other) {
            if ($other->getEnd() > $start) {
                // we're doing this to maintain the original line indexes
                $line->setContent($other->getContent() . $line->getContent());
                $other->remove();
            }
        }

        return $doc->getContent();
    }
}
