import app from 'flarum/admin/app';
import SettingsPage from './components/SettingsPage';
import extendStatusWidget from './extendStatusWidget';
import extendDashboardPage from './extendDashboardPage';

app.initializers.add('blomstra/horizon', () => {
  app.extensionData.for('blomstra-horizon').registerPage(SettingsPage);
  extendStatusWidget();
  extendDashboardPage();
});
