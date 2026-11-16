<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'ホームページへ',
    'search' => 'サイト内を検索',
    'search_button' => '検索',
    '403' => [
        'title' => 'アクセスが拒否されました',
        'message' => 'このページを開く権限がありません。',
    ],
    '404' => [
        'title' => 'ページが見つかりません',
        'message' => 'アドレスが間違っているか、ページが移動または削除された可能性があります。',
    ],
    '419' => [
        'title' => 'ページの有効期限が切れました',
        'message' => 'ページが長時間開かれていました。前のページに戻り、再読み込みしてもう一度お試しください。',
    ],
    '429' => [
        'title' => 'リクエストが多すぎます',
        'message' => '1分ほど待ってから、もう一度お試しください。',
    ],
    '500' => [
        'title' => '問題が発生しました',
        'message' => 'エラーは記録されました。数分後にもう一度お試しください。',
    ],
    '503' => [
        'title' => 'まもなく再開します',
        'message' => 'サイトを更新しています。数分後にもう一度お越しください。',
    ],
    '4xx' => [
        'title' => 'リクエストを完了できませんでした',
        'message' => 'アドレスを確認するか、ホームページからやり直してください。',
    ],
    '5xx' => [
        'title' => 'サーバーが応答できませんでした',
        'message' => '数分後にもう一度お試しください。',
    ],
];
