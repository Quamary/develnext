package org.develnext.php;

import org.develnext.jphp.swing.SwingExtension;
import org.develnext.php.ext.UIDockingExtension;
import org.develnext.php.ext.UISyntaxExtension;
import php.runtime.env.CompileScope;

public class DevelNextExtension extends SwingExtension {
    public final static String NAMESPACE = "develnext\\";

    @Override
    public String getName() {
        return "DevelNext";
    }

    @Override
    public String getVersion() {
        return "4.0";
    }

    @Override
    public String[] getRequiredExtensions() {
        return new String[]{
                SwingExtension.class.getName(),
                LocalizationExtension.class.getName(),
                UIDockingExtension.class.getName(),
                UISyntaxExtension.class.getName()
        };
    }

    @Override
    public void onRegister(CompileScope scope) {
        registerNativeClass(scope, WrapResizableBorder.class);
        registerNativeClass(scope, WrapResizableContainer.class);
        registerNativeClass(scope, WrapDesignContainer.class);

        registerNativeClass(scope, WrapComponentResizer.class);
        registerNativeClass(scope, WrapComponentMover.class);
    }
}