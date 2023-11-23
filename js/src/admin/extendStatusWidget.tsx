import app from 'flarum/admin/app';
import { extend } from 'flarum/common/extend';
import StatusWidget from 'flarum/admin/components/StatusWidget';

export default function extendStatusWidget() {
  extend(StatusWidget.prototype, 'items', function (items) {
    items.add('version-redis', [<strong>Redis</strong>, <br />, app.data.redisVersion], 75);
  });
}
