<?php

/*
 * https://glyphicons.bootstrapcheatsheets.com/
 */

namespace app\components;

class Icon
{
    private static $_icons = [
        'home' => ['label' => 'HOME', 'class' => 'fa-solid fa-house'],
        'login' => ['label' => 'ログイン', 'class' => 'fa-solid fa-right-to-bracket'],
        'logout' => ['label' => 'ログアウト', 'class' => 'fa-solid fa-right-from-bracket'],
        'users' => ['label' => 'ユーザ一覧', 'class' => 'fa-solid fa-people-group'],
        'ok' => ['label' => 'OK', 'class' => 'fa-solid fa-check'],
        'search' => ['label' => '検索', 'class' => 'fa-solid fa-magnifying-glass'],
        'clear' => ['label' => 'クリア', 'class' => 'fa-solid fa-asterisk'],
        'view' => ['label' => '閲覧', 'class' => 'fa-solid fa-eye'],
        'update' => ['label' => '編集', 'class' => 'fa-solid fa-pencil'],
        'user' => ['label' => 'ユーザ', 'class' => 'fa-solid fa-user'],
        'user-view' => ['label' => '閲覧', 'class' => 'fa-solid fa-user'],
        'admin' => ['label' => '管理', 'class' => 'fa-solid fa-wrench'],
        'user-admin' => ['label' => '管理', 'class' => 'fa-solid fa-wrench'],
        'user-create' => ['label' => 'ユーザを新規登録', 'class' => 'fa-solid fa-user-plus'],
        'person' => ['label' => '関係者', 'class' => 'fa-solid fa-address-book'],
        'contact' => ['label' => '連絡先', 'class' => 'fa-solid fa-address-card'],
        'delete' => ['label' => '削除', 'class' => 'fa-solid fa-trash-can'],
        'cancel' => ['label' => 'キャンセル', 'class' => 'fa-solid fa-xmark'],
        'end-edit' => ['label' => '編集を終了（キャンセル）', 'class' => 'fa-solid fa-turn-up'],
        'rbac' => ['label' => 'RBAC', 'class' => 'fa-solid fa-gear'],
        'role' => ['label' => '権限', 'class' => 'fa-solid fa-gear'],
        'register' => ['label' => 'メモを新規登録', 'class' => 'fa-solid fa-pencil'],
        'category' => ['label' => 'カテゴリ', 'class' => 'fa-solid fa-folder'],
        'category-create' => ['label' => 'カテゴリを新規登録', 'class' => 'fa-solid fa-folder-plus'],
        'prev' => ['label' => '前へ', 'class' => 'fa-solid fa-caret-left'],
        'next' => ['label' => '次へ', 'class' => 'fa-solid fa-caret-right'],
        'backward' => ['label' => '前へ', 'class' => 'fa-solid fa-caret-left'],
        'forward' => ['label' => '次へ', 'class' => 'fa-solid fa-caret-right'],
        'fast-backward' => ['label' => '先頭へ', 'class' => 'fa-solid fa-backward-step'],
        'fast-forward' => ['label' => '末尾へ', 'class' => 'fa-solid fa-forward-step'],
        'up' => ['label' => '上へ', 'class' => 'fa-solid fa-caret-up'],
        'down' => ['label' => '下へ', 'class' => 'fa-solid fa-caret-down'],
        'copy' => ['label' => '複製', 'class' => 'fa-solid fa-clone'],
        'memo' => ['label' => 'メモ', 'class' => 'fa-solid fa-book'],
        'left' => ['label' => '前へ', 'class' => 'fa-solid fa-left-long'],
        'right' => ['label' => '次へ', 'class' => 'fa-solid fa-right-long'],
        'go-back' => ['label' => '一覧へ戻る', 'class' => 'fa-solid fa-turn-up'],
        'link' => ['label' => 'リンク選択','class' => 'fa-solid fa-link'],
        'unlink' => ['label' => 'リンク解除','class' => 'fa-solid fa-link-slash'],
        'plus-c' => ['label' => '新規登録','class' => 'fa-solid fa-circle-plus'],
        'plus-s' => ['label' => '新規登録','class' => 'fa-solid fa-square-plus'],
        'plus' => ['label' => '追加','class' => 'fa-solid fa-plus'],
        'succeed' => ['label' => '引継','class' => 'fa-solid fa-user-group'],
        'mountain' => ['label' => '山林','class' => 'fa-solid fa-mountain'],
        'tree' => ['label' => '山林','class' => 'fa-solid fa-tree'],
        'field' => ['label' => '農地','class' => 'fa-solid fa-seedling'],
        'map-location' => ['label' => '地図','class' => 'fa-solid fa-location-dot'],
        'excel' => ['label' => 'Excel','class' => 'fa-solid fa-file-excel'],
    ];

    /**
     * @param string $text
     * @param string|null $title
     * @return string
     */
    public static function getIcon(string $text, string $title = null): string
    {
        if ($title === null) {
            $title = self::getLabel($text);
        }
        if (isset(self::$_icons[$text])) {
            return '<i class="' . self::$_icons[$text]['class'] . '" title="' . $title . '"></i>';
        } else {
            return $text;
        }
    }

    /**
     * @param $text
     * @return string
     */
    public static function getLabel($text): string
    {
        if (isset(self::$_icons[$text])) {
            return self::$_icons[$text]['label'];
        } else {
            return $text;
        }
    }

    /**
     * @param string $text
     * @param string|null $title
     * @return string
     */
    public static function getIconAndLabel(string $text, string $title = null): string
    {
        if ($title === null) {
            $title = self::getLabel($text);
        }
        return self::getIcon($text, $title) . ' ' . $title;
    }

    /**
     * @param string $text
     * @param string|null $title
     * @param string $break_point
     * @return string
     */
    public static function getBtnText(string $text, string $title = null, string $break_point = 'md'): string
    {
        if ($title === null) {
            $title = self::getLabel($text);
        }
        return self::getIcon($text, $title) . '<span class="d-none d-' . $break_point . '-inline"> ' . $title . '</span>';
    }
}