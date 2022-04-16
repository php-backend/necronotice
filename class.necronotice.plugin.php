<?php
declare(strict_types=1);

/**
 * Vanilla Forum Plugin to Show a notice above comment input box if the last reply is X days old.
 *
 * @author PHP Backend
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU GPL v2
 * @package NecroNotice
 */
class NecroNoticePlugin extends Gdn_Plugin
{

    public function discussionController_beforeBodyField_handler($sender)
    {
        $daysTilDeath = c('necronotice.daysTilDeath', 30);
        $fontColorCode = c('necronotice.fontColorCode');
        $backgroundColorCode = c('necronotice.backgroundColorCode');
        $noticeText = c('necronotice.notice');

        $lastCommentDate = new DateTimeImmutable($sender->EventArguments['Discussion']->DateLastComment);

        $commentAllowedTill = $lastCommentDate->modify('+ ' . $daysTilDeath . ' days');

        $now = new DateTimeImmutable();

        if ($now > $commentAllowedTill) {
            // discussion is now in necro state

            $noticeText = sprintf($noticeText, $now->diff($lastCommentDate)->format('%a'));

            echo <<<HTML
            <div style="border: 1px solid #a00;
                color: {$fontColorCode};
                background: {$backgroundColorCode};
                padding: 6px 10px;
                display: block;
                border-radius: 2px;
                margin-bottom: 1em;"
            >
                {$noticeText}
            </div>
            HTML;
        }
    }

    public function setup()
    {
        saveToConfig('necronotice.daysTilDeath', 30);
        saveToConfig('necronotice.fontColorCode', '#fff');
        saveToConfig('necronotice.backgroundColorCode', '#d50a0a');

        saveToConfig('necronotice.notice', 'This post had last comment %s days ago.<br>This Post is now in necro state.');
    }
}
