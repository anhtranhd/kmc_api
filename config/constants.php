<?php

define('API_HTTP_OK', 200);
define('API_HTTP_FAIL', 201);
define('API_HTTP_BAD_REQUEST', 400);
define('API_HTTP_UNAUTHORIZED', 401);
define('API_HTTP_FORBIDDEN', 403);
define('API_HTTP_NOT_FOUND', 404);
define('API_HTTP_FAILED_LOGIC', 422);
define('API_HTTP_SERVER_ERROR', 500);

define('STATUS_SUCCESS', 'SUCCESS');
define('STATUS_ERROR', 'ERROR');

define('DIR_STORAGE', 'storage');
define('DIR_UPLOAD', 'upload');
define('DIR_ARTUFACT', 'artifact');
define('DIR_SPOT', 'spot');
define('DIR_AR', 'ar');
define('DIR_DETAIL', 'detail');
define('DIR_ICON', 'icon');
define('DIR_SLIDE', 'slide');
define('DIR_CHARACTER', 'character');
define('DIR_CONTENT', 'content');
define('DIR_MEDIA', 'media');
define('DIR_TARGET', 'target');
define('DIR_IMAGE', 'image');
define('DIR_VIDEO', 'video');
define('DIR_AUDIO', 'audio');
define('DIR_3D', '3d');
define('DIR_artifact_REVIEW', 'review');
define('DIR_KAR', 'kar');
define('DIR_ASYLUM', 'asylum');
define('DIR_HOSPITAL', 'hospital');
define('DIR_MEDIA_CROP', 'media_crop');
define('DIR_USER', 'user');

const
ROWS_PER_PAGE = 20,
STATUS_ACTIVE = 1,
STATUS_INACTIVE = 0,
ON = 1,
OFF = 0;

const
OBJ_TYPE_LANGUAGE = 'language',
OBJ_TYPE_SPOT = 'spot',
OBJ_TYPE_AR_CONTENT = 'ar_contents',
OBJ_TYPE_AR_MEDIA = 'media',
OBJ_TYPE_TARGET = 'target';

const
OBJ_NAME_NAME = 'name',
OBJ_NAME_SERIALIZED_DATA = 'serialized_data';

const
LANG_VIETNAMESE_CODE = 'vi';

const
TAB_MEDIA_IMAGE = 'image',
TAB_MEDIA_AUDIO = 'audio',
TAB_MEDIA_VIDEO = 'video',
TAB_MEDIA_3D = '3d';

const AR_LANGUAGE_COMMON = 0;

const
CONTENT_VALUE_DEFAULT = '{"version":"2.4.4","objects":[]}';

const
RADIUS_REQUEST = 1.5; //km
