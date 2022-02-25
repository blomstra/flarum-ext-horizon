import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import LinkButton from 'flarum/common/components/LinkButton';

export default class SettingsPage extends ExtensionPage {
  content() {
    const horizonUrl = app.forum.attribute('adminUrl') + '/horizon';
    return (
      <div className="container">
        <div className="HorizonSettingsPage">
          <LinkButton className="Button Button--primary" href={horizonUrl} external={true} target="_blank">
            Open Horizon dashboard
          </LinkButton>
        </div>
      </div>
    );
  }
}
