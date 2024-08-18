#!/bin/bash

TIME="10"
URL="https://api.telegram.org/bot6918458120:AAE6HqPsKha9tyZ_VzvjhNmVjT0iRoaYqYs/sendMessage"
TEXT="Deploy status: $1%0A%0AProject:+$CI_PROJECT_NAME%0AURL:+$CI_PROJECT_URL/pipelines/$CI_PIPELINE_ID/%0ABranch:+$CI_COMMIT_REF_SLUG%0AUser:+$GITLAB_USER_NAME%0ACommit:+$CI_COMMIT_TITLE"

curl -s --max-time $TIME -d "chat_id=6066220454&disable_web_page_preview=1&text=$TEXT" $URL > /dev/null
