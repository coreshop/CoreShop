<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <?=$this->template("email/head.php")?>
    <body>
    <table class="body">
        <tr>
            <td class="center" align="center" valign="top">
                <center>

                    <table class="row header">
                        <tr>
                            <td class="center" align="center">
                                <center>

                                    <table class="container">
                                        <tr>
                                            <td class="wrapper last">

                                                <table class="twelve columns">
                                                    <tr>
                                                        <td class="six sub-columns">
                                                            <img src="/static/images/logo.png" title="Spice Shoppe" alt="Spice Shoppe" class="img-responsive">
                                                        </td>
                                                        <td class="six sub-columns last" style="text-align:right; vertical-align:middle;">
                                                            <span class="template-label">NEW ORDER</span>
                                                        </td>
                                                        <td class="expander"></td>
                                                    </tr>
                                                </table>

                                            </td>
                                        </tr>
                                    </table>

                                </center>
                            </td>
                        </tr>
                    </table>

                    <table class="container">
                        <tr>
                            <td>
                                <?= $this->layout()->content ?>


                                <table class="row footer">
                                    <tr>
                                        <td class="wrapper">

                                            <table class="six columns">
                                                <tr>
                                                    <td class="left-text-pad">

                                                        <h5>Connect With Us:</h5>

                                                        <table class="tiny-button facebook">
                                                            <tr>
                                                                <td>
                                                                    <a href="#">Facebook</a>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                        <br>

                                                        <table class="tiny-button twitter">
                                                            <tr>
                                                                <td>
                                                                    <a href="#">Twitter</a>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                        <br>

                                                        <table class="tiny-button google-plus">
                                                            <tr>
                                                                <td>
                                                                    <a href="#">Google +</a>
                                                                </td>
                                                            </tr>
                                                        </table>

                                                    </td>
                                                    <td class="expander"></td>
                                                </tr>
                                            </table>

                                        </td>
                                        <td class="wrapper last">

                                            <table class="six columns">
                                                <tr>
                                                    <td class="last right-text-pad">
                                                        <h5>Contact Info:</h5>
                                                        <p>Phone: +436603617785</p>
                                                        <p>Email: <a href="mailto:hseldon@trantor.com">get@lineofcode.at</a></p>
                                                    </td>
                                                    <td class="expander"></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                </table>


                                <table class="row">
                                    <tr>
                                        <td class="wrapper last">

                                            <table class="twelve columns">
                                                <tr>
                                                    <td align="center">
                                                        <center>
                                                            <p style="text-align:center;"><a href="#">Terms</a> | <a href="#">Privacy</a> | <a href="#">Unsubscribe</a></p>
                                                        </center>
                                                    </td>
                                                    <td class="expander"></td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                </table>

                                <!-- container end below -->
                            </td>
                        </tr>
                    </table>

                </center>
            </td>
        </tr>
    </table>
    </body>
</html>