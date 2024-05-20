import { NestedStringArray } from '@askvortsov/rich-icu-message-formatter';
import app from 'flarum/admin/app';
import DashboardWidget, { IDashboardWidgetAttrs } from 'flarum/admin/components/DashboardWidget';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Button from 'flarum/common/components/Button';
import type Mithril from 'mithril';
import LinkButton from 'flarum/common/components/LinkButton';
import Tooltip from 'flarum/common/components/Tooltip';
import Switch from 'flarum/common/components/Switch';
import icon from 'flarum/common/helpers/icon';

export default class HorizonStatsWidget extends DashboardWidget {
  loading = true;
  data: any = {};
  autoRefreshEnabled = false;
  autoRefreshInterval?: number;

  oncreate(vnode: Mithril.Vnode<IDashboardWidgetAttrs>) {
    super.oncreate(vnode);
    this.loadHorizonStats();
  }

  onremove() {
    this.clearAutoRefresh();
  }

  async loadHorizonStats() {
    this.loading = true;
    m.redraw();
    const data = await app.request({
      method: 'GET',
      url: app.forum.attribute('adminUrl') + '/horizon/api/stats',
    });

    this.data = data;
    this.loading = false;
    m.redraw();
  }

  toggleAutoRefresh(enabled: boolean) {
    this.autoRefreshEnabled = enabled;
    if (enabled) {
      this.setAutoRefresh();
    } else {
      this.clearAutoRefresh();
    }
  }

  setAutoRefresh() {
    this.clearAutoRefresh();
    this.autoRefreshInterval = setInterval(() => this.loadHorizonStats(), 5000) as unknown as number;
  }

  clearAutoRefresh() {
    if (this.autoRefreshInterval) {
      clearInterval(this.autoRefreshInterval as unknown as NodeJS.Timeout);
      this.autoRefreshInterval = undefined;
    }
  }

  className() {
    return 'HorizonStatsWidget';
  }

  content() {
    return (
      <div className="HorizonStatsWidget-container">
        <div className="HorizonStatsWidget-header">
          <h4 className="HorizonStatsWidget-title">{app.translator.trans('blomstra-horizon.admin.stats.heading')}</h4>
          <div className="HorizonStatsWidget-controls">
            <Tooltip text={app.translator.trans('blomstra-horizon.admin.stats.refresh')}>
              <Button
                className="Button Button--icon"
                icon="fas fa-sync-alt"
                onclick={() => this.loadHorizonStats()}
                disabled={this.loading || this.autoRefreshEnabled}
              />
            </Tooltip>

            <LinkButton
              className="Button"
              icon="fas fa-external-link-alt"
              href={app.forum.attribute('adminUrl') + '/horizon'}
              target="_blank"
              external={true}
            >
              {app.translator.trans('blomstra-horizon.admin.stats.full_dashboard')}
            </LinkButton>
          </div>
        </div>
        <div className="HorizonStatsWidget-grid">{this.renderStatsSection()}</div>
        <div className="HorizonStatsWidget-footer">
          <Switch state={this.autoRefreshEnabled} onchange={this.toggleAutoRefresh.bind(this)} loading={this.loading}>
            {app.translator.trans('blomstra-horizon.admin.stats.auto_refresh')}
          </Switch>
        </div>
      </div>
    );
  }

  renderStatsSection() {
    const { jobsPerMinute, recentJobs, recentlyFailed, status, processes, queueWithMaxRuntime, queueWithMaxThroughput } = this.data;
    const redis_stats = this.data.redis_stats ?? {};

    return (
      <>
        {this.renderStatusIndicator(status)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.redis-used-memory'), redis_stats.memory_used)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.redis-peak-memory'), redis_stats.memory_peak)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.redis-max-memory'), redis_stats.memory_max ?? 'auto')}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.redis-cpu-user'), redis_stats.cpu_user + '%')}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.redis-cpu-sys'), redis_stats.cpu_sys + '%')}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.jobs-per-minute'), jobsPerMinute)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.jobs-past-hour'), recentJobs)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.failed-last-seconds'), recentlyFailed)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.total-processes'), processes)}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.max-wait-time'), '-')}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.max-runtime'), queueWithMaxRuntime ?? '-')}
        {this.renderStat(app.translator.trans('blomstra-horizon.admin.stats.data.max-throughput'), queueWithMaxThroughput ?? '-')}
      </>
    );
  }

  renderStat(label: NestedStringArray, value: string) {
    return (
      <div className="HorizonStatsWidget-stat">
        <small>{label}</small>
        <p>{value || !this.loading ? value : <LoadingIndicator size="small" display="inline" />}</p>
      </div>
    );
  }

  renderStatusIndicator(status: string | null) {
    const iconClass = status === 'running' ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';

    return (
      <div className="HorizonStatsWidget-stat">
        <small>{app.translator.trans('blomstra-horizon.admin.stats.data.status.label')}</small>
        <p>
          {icon(iconClass)} {status ? app.translator.trans(`blomstra-horizon.admin.stats.data.status.${status}`) : ''}
        </p>
      </div>
    );
  }
}
