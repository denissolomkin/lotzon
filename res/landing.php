<!doctype html>
<html>
<head>
	<title><?=$seo['Title'];?></title>
    <meta name="description" content="<?=$seo['Description'];?>">
    <meta name="keywords" content="<?=$seo['Keywords'];?>" />
	
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    
    <link rel="icon" href="/res/img/favicones/favicon.png_128x128.png?v=666" type="image/png"/>
    <meta content="/res/img/favicones/favicon.png_128x128.png?v=666" itemprop="image">
    <link href="/res/img/favicones/favicon.png.ico?v=666" rel="shortcut icon">

    <link rel="stylesheet" href="/res/css/landing/landing_screen.css?v2">
    <?php // if (isset($isMobile)) { if($isMobile) { ?>
        <!-- <link rel="stylesheet" href="/res/css/landing/landing_mobile.css"> -->
    <?php // } else { ?>
        <!-- <link rel="stylesheet" href="/res/css/landing/landing_screen.css"> -->
    <?php //} } ?>
	<script src="/res/js/libs/jquery-2.1.4.min.js"></script>
	<script src="/res/js/landing/jquery.magnific-popup.min.js"></script>
    <script> player = <?php echo json_encode($player, JSON_PRETTY_PRINT);?>;</script>

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?=$seo['Title'];?>">
    <meta itemprop="description" content="LOTZON. Выигрывает каждый!">
    <meta itemprop="image" content="https://lotzon.com/res/img/lotzon_soc.jpg?rnd=<?=rand()?>">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="https://lotzon.com/res/img/lotzon_soc.jpg?rnd=<?=rand()?>">
    <meta name="twitter:title" content="<?=$seo['Title'];?>">
    <meta name="twitter:description" content="LOTZON. Выигрывает каждый!">
    <!-- Twitter summary card with large image must be at least 280x150px -->
    <meta name="twitter:image:src" content="https://lotzon.com/res/img/lotzon_soc.jpg?rnd=<?=rand()?>">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?=$seo['Title'];?>" />
    <meta property="og:image" content="https://lotzon.com/res/img/lotzon_soc.jpg?rnd=<?=rand()?>" />
    <meta property="og:description" content="LOTZON. Выигрывает каждый!" />

</head>
<body class="landing">
    <div class="header clearfix">
        <div class="wrapper">
            <div class="header-left i-lotzon"></div> 
            
            <div class="header-right">
                <div class="popup-msg" >о нас</div>
                <div class="i-play popup-vimeo" href="https://vimeo.com/114883943">видео ролик</div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="wrapper">
            <div class="wrapper-inner">
                <!-- Information -->
                <div class="left-content">
                    <h1 class="landing-title">Джекпот <?php echo number_format($slider['jackpot'], 0, ' ', ' '); ?>  <?php echo $player['currency']['iso'];?></h1>
                    <p class="lending-text">
                        Мы не азартная игра, так как участие полностью бесплатное!<br><br>
                        У каждого участника есть возможность выиграть деньги или <br>
                        ценные призы, участвовать в конкурсах, общаться, заводить <br>
                        новые знакомства. <br>
                    </p>
                    <p class="landing_middle"><?php echo number_format($slider['players'], 0, ' ', ' '); ?> <span>зарегистрированных</span></p>
                    <p class="landing_large"><? echo number_format($slider['sum'], 0, ' ', ' '); ?> <span><?php echo $player['currency']['iso'];?> уже выплачено</span></p>
                </div>
                <!-- LogIn -->
                <div class="right-content">
                    <div class="box password-recovery-box">
                        <!-- REPASSWORD FORM -->
                        <div class="box-title">
                            <i class="i-arrow-slim-left back"></i>
                            <span>Востановление пароля</span>
                        </div>
                        <form name="rec-pass">
                            <div id="pass-rec-form">
                                <div class="ib-l">
                                    <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email">
                                    <div class="alert"></div>
                                </div>
                                <div class="input-text">
                                    <!-- <div class="hidden-text">Новый пароль выслан на указанный email.</div> -->
                                    <div class="text">На этот email будет выслано письмо для восстановления пароля</div>
                                    <div class="alert"></div>
                                </div>
                                
                                <div class="s-b">
                                    <input type="submit" class="sb_but default disabled" disabled="" value="Восстановить">
                                </div>
                            </div>
                        </form>
                        <div id="pass-rec-form-success">
                            <div class="input-text">
                                <div class="text">Новый пароль выслан на указанный email.</div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="box login-box">
                        <!-- LOGIN FORM -->
                        <form id="login-block-form" name="login">
                            <div id="login-form">
                                <div class="ib-l">
                                    <input placeholder="email" autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email" value="">
                                </div>
                                <div class="ib-p">
                                    <input placeholder="Пароль" autocomplete="off" spellcheck="false" type="password" class="m_input" name="password" placeholder="Пароль">
                                </div>
                                <div class="input-text">
                                    <div class="alert">
                                        <span>Такой email не зарегистрирован или пароль не верен</span>
                                    </div>
                                </div>
                                <div class="ch-b-bk">
                                    
                                    <div class="ch-b">
                                        <input type="checkbox" id="remcheck" hidden="">
                                        <label for="remcheck">Запомнить</label>
                                    </div>
                                    <a href="javascript:void(0)" id="rec-pass" class="r-p">забыли пароль?</a>
                                </div>
                                <div class="s-b">
                                    <input type="submit" class="sb_but default" value="Войти">
                                </div>
                                <div class="sl-bk">
                                    <a href="./auth/Facebook?method=log-in" class="i-Facebook-simple"></a>
                                    <a href="./auth/Vkontakte?method=log-in" class="i-Vkontakte-simple"></a>
                                    <a href="./auth/Odnoklassniki?method=log-in" class="i-Odnoklassniki-simple"></a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="box">
                        <div class="text">
                            <p class="title">Впервые в Лотзон? <span>Присоединяйтесь</span></p>
                            <p>У нас выигрывает каждый! Для этого нужно только зарегистрироваться.</p>
                        </div>

                        <button class="landing_button gold go-play">Играть Бесплатно</button>
                        <span class="unregistred"><a href="/?guest=1">Войти как гость</a></span>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- COMMENTS -->
        <div class="comments">
            <div class="comment">
                <div class="msg">
                    <div class="msg-inner">
                        Деньги пришли через 2 дня после заказа . Сайт отличный
                    </div>
                </div>
                <div class="uinfo">
                    <div class="img">
                        <img src="/res/css/img/3f6f3106a467a08b30b325def5526482.jpg?b">
                    </div>
                    <div class="name">Яна Иванова</div>
                </div>
            </div>
            <div class="comment">
                <div class="msg">
                    <div class="msg-inner">
                        Уже не первый раз вывожу денежные средства, всегда приходят вовремя, без задержек, спасибо большое)
                    </div>
                </div>
                <div class="uinfo">
                    <div class="img">
                        <img src="/res/css/img/df7f03ffb97e701b0abcc9d8d0e79d3f.jpg?b">
                    </div>
                    <div class="name">Светлана Данилина</div>
                </div>
            </div>
            <div class="comment">
                <div class="msg">
                    <div class="msg-inner">
                        Счет пополнен! Уже, даже и не знаю какая по счету оплата :) Спасибо LOTZON! Успеха проекту
                    </div>
                </div>
                <div class="uinfo">
                    <div class="img">
                        <img src="/res/css/img/79f6fab49b8d430c8a3b530e22b81547.jpg?b">
                    </div>
                    <div class="name">Олег Горбанетно</div>
                </div>
            </div>
        </div>
    </div>

    <!-- POPUP -->
    <div class="popup" id="login-block" style="display: none;">
        <div class="popup-inner">
            <i class="popup-close i-x-slim"></i>
            
            <!-- REGISTRATION FORM -->
            <form name="register" data-ref="<?=$ref?>">
                <div id="reg-form">

                    <div class="title box-inner">Регистрация нового участника</div>
                    <div class="box-inner">
                        <div class="ib-l">
                            <input autocomplete="off" spellcheck="false" type="email" class="m_input" name="login" placeholder="Ваш email">
                            <div class="input-text">
                                <div class="alert">Этот email уже зарегистрирован</div>
                                <div class="hidden-text" id="reg-succ-txt">Информация для завершения регистрации выслана на Ваш email.</div>
                                <div class="text">Напишите ваш email, на него будет выслано письмо для завершения регистрации</div>
                            </div>
                        </div>
                        <div class="s-b">
                            <input type="submit" disabled="" class="sb_but gold disabled" value="Продолжить">
                        </div>
                        <!-- Add class "disabled" -->
                        <div class="sl-bk">
                            <div class="lines-between">
                                <span>или через</span>
                            </div>
                            <div>
                                <a href="./auth/Facebook?method=user<?=($ref?'&ref='.$ref:'')?>" class="i-Facebook-simple">Facebook</a>
                                <a href="./auth/Vkontakte?method=user<?=($ref?'&ref='.$ref:'')?>" class="i-Vkontakte-simple">ВКонтакте</a>
                                <a href="./auth/Odnoklassniki?method=user<?=($ref?'&ref='.$ref:'')?>" class="i-Odnoklassniki-simple">Одноклассники</a>
                            </div>
                        </div>
                        
                        <div class="bottom license">
                            <span >Регистрируясь, вы принимаете</span>
                            <a class="rs-sw" >пользовательское соглашение</a>
                        </div>
                        
                    </div>
                </div>
            </form>

            <!-- REGISTRATION compleet -->
            <form name="email-send">    
                <div class="title box-inner">
                    <i class="i-arrow-slim-left back"></i>
                    <span>подтверждение email</span>
                </div>
                <div class="box-inner">
                    <div class="input-text">
                        <div class="text">На почту <span class="current-mail"></span> отправлено письмо для подтверждения регистрации.</div>
                    </div>
                    <div class="bottom">
                        <span >Если вы не получили письмо в течении 5 минут проверьте папку «спам». Если письма нет, нажмите <a class="resend" >отправить повторно</a></span>
                    </div>
                </div>
            </form>
            
            <!-- Social registration -->
            <form name="social_register" data-ref="<?=$ref?>">
                
                <div class="title box-inner">EMAIL</div>
                <div class="box-inner">
                    <div class="ib-l">
                        <div class="text">Письмо для подтверждения регистрации будет выслано на email <span class="current-mail"></span></div>
                        <p>или</p>
                        <input autocomplete="off" spellcheck="false" type="email" class="m_input_uncheck" name="email" placeholder="выслать на другой email">
                        <div class="input-text">
                            <div class="alert">Этот email уже зарегистрирован</div>
                            <div class="hidden-text" id="reg-succ-txt">Информация для завершения регистрации выслана на Ваш email.</div>
                            <!-- <div class="text">Напишите ваш email, на него будет выслано письмо для завершения регистрации</div> -->
                        </div>
                    </div>
                    <div class="s-b">
                        <input type="submit" class="sb_but gold" value="Продолжить">
                    </div>
                </div>
            </form>

            <!-- Social error -->
            <form name="social_error">
                
                <div class="title box-inner">ДОСТУП ЗАБЛОКИРОВАН</div>
                <div class="box-inner">
                    <div class="ib-l">
                        <div class="text">Аккаунт заблокирован за нарушение правил участия.</div>
                    </div>
                </div>
            </form>
            <!-- Social unexpected_error -->
            <form name="unexpected_error">
                
                <div class="title box-inner">ПРОИЗОШЛА ОШИБКА</div>
                <div class="box-inner">
                    <div class="ib-l">
                        <div class="text">Повторите попытку позже.</div>
                    </div>
                </div>
            </form>
            <!-- Social exist -->
            <form name="social_exist">
                
                <div class="title box-inner">АККАУНТ УЖЕ ЗАРЕГИСТРИРОВАН</div>
                <div class="box-inner">
                    <div class="ib-l">
                        <div class="text">Соцсеть через которую Вы хотите зарегистрироватся уже есть в нашей базе. <br><br>Нажмите кнопку «продолжить» для входа в аккаунт</div>
                    </div>
                    <div class="s-b">
                        <input type="submit" class="sb_but gold" value="Продолжить">
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- POPUP -->
    <!-- INFO POPUP -->
    <div class="info-popup">
        <div class="close-box">
            <div class="close-inner">
                <i class="i-x-slim"></i>
            </div>
        </div>
        <div class="inner">
            <div class="box about">
                <h2>О НАС</h2>
                <div>
                    <div>Главный принцип и философия нашего проекта - Бесплатное участие.</div><br>
                    <div>Мы решили сделать революцию в просмотре рекламы, за счет которой каждый участник проекта LOTZON сможет бесплатно играть и выиграть реальные деньги и призы.</div><br>
                    <div>Теперь подробней, о том как это вообще возможно.</div><br>
                    <div>Находясь на разных сайтах, вы просматриваете рекламу и заработанные от рекламы деньги владелец сайта ложит себе в карман. Такой принцип существует везде, интернет, телевидение, плакаты, журналы и т.д.</div><br>
                    <div>Что получаете вы? Ничего особого - общение, просмотр новостей, просмотр фотографий, видео, игры.</div><br>
                    <div>Мы решили пойти по новому пути и дать возможность каждому участнику LOTZON получить часть от прибыли заработанной на рекламе. Регистрируясь вы получите доступ к разным играм, в которых можно выиграть реальные деньги и призы, испытаете чувство драйва и азарта при этом никаких финансовых вложений от вас не требуется.</div><br>
                    <div>А еще у нас постоянные конкурсы, общение, друзья и новые знакомства. Мы постоянно работаем над развитием сайта, стараемся радовать вас новыми возможностями.</div><br>
                    <div>Мы не обещаем вам «золотые горы» как делают другие сайты,
                        но при этом только отнимают заработанные вами деньги. 
                        У нас многое зависит от вашей удачи, смекалки, интуиции.<br><br>

                        И самое главное, каждый день проходит основной розыгрыш,
                        в котором у каждого участника есть шанс выиграть главный приз - 
                        <?php echo number_format($slider['jackpot'], 0, ' ', ' '); ?>  <?php echo $player['currency']['iso'];?>.
                    </div><br>
                    <div>LOTZON. Выигрывает каждый!</div>
                </div>
            </div>
            <div class="box license">
                                <h2>Условия участия</h2>
                                <div>Соглашение<br></div>

                                <div>Внимание! Если Вы начали участвовать в игровом процессе на сайте Lotzon.com, то этим вы принимаете настоящее Соглашение и подтверждаете свое согласие со всеми его условиями без каких-либо ограничений. Также, подтверждаете, что вы обладаете всеми необходимыми гражданскими правами и дееспособностью для участия в данной игре.</div>

                                <div><br></div>

                                <div>Настоящее Соглашение регулирует отношения между Администрацией сайта Lotzon (http://lotzon.com) (далее - Сайт), именуемое в дальнейшем «Администрация» и Вами, Участником Игры (далее «Участник»), в отношении Игры.</div>

                                <div><br></div>

                                <div>1.<span class="Apple-tab-span" style="white-space: pre;">  </span>Термины, используемые в настоящем Соглашении</div>

                                <div><br></div>

                                <div>1.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Игра&nbsp;— онлайн-игра на Сайте, доступ Участников к которой осуществляется исключительно Администрацией. Участие Участников в Игре происходит в интерактивном (онлайн) режиме, посредством подключения Участника через всемирную сеть Интернет к Игровым ресурсам Администрация.</div>

                                <div>Принцип функционирования Игры основывается на общеизвестной модели Free-To-Play (бесплатная игра), что означает предоставление Участнику права использовать Игру путем участия в Игре без внесения абонентской платы и без каких-либо иных обязательных платежей, которые необходимы для принятия участия в Игре.</div>

                                <div>Администрация является обладателем необходимого объема прав на Игру и на все ее элементы, взятые как в отдельности, так и в совокупности.</div>

                                <div><br></div>

                                <div>1.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Интернет сайт Игры&nbsp;— интернет сайт, расположенный по сетевому адресу http://lotzon.com, предоставляющий Участнику доступ к ресурсам Администрации, в том числе для участия Участника в Игре. Администрация размещает информацию, обязательную для Участников, на Сайте.</div>

                                <div><br></div>

                                <div>1.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Игровые ресурсы&nbsp;(Ресурсы) — все сервера, любое программное обеспечение и/или базы данных, имеющие отношение к Игре, расположенные, в том числе, в домене Lotzon.com и его поддоменах.</div>

                                <div><br></div>

                                <div>1.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация&nbsp;— является обладателем необходимого объема прав на Игру и предоставляет право использования Игры Участникам и осуществляет доведение до всеобщего сведения, распространение, оперирование, обслуживание, администрирование Игры. На условиях настоящего Соглашения Администрация предоставляет право использования Игры и доступ Участникам к Игре. Администрация является Стороной настоящего Соглашения.</div>

                                <div><br></div>

                                <div>1.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Участник&nbsp;— физическое лицо, обладающее необходимыми дееспособностью и гражданскими правами для заключения настоящего Соглашения, принимающее участие в Игре и которому в соответствии с настоящим Соглашением предоставляется право на использование Игры в предусмотренных настоящим Соглашением пределах. Участник является Стороной настоящего Соглашения.</div>

                                <div><br></div>

                                <div>1.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>Передача прав использования Игры&nbsp;— предоставление Администрацией Участнику прав использования Игры, а также доступа к Ресурсам, в том числе, права ее воспроизведения на ЭВМ, доступа к участию в Игре, использование ее возможностей, на условиях и в порядке, определенных настоящим Соглашением и стандартным режимом функционирования Игры. Предоставление Участнику прав использования Игры, осуществляется Администрация на безоплатной основе.</div>

                                <div><br></div>

                                <div>1.7.<span class="Apple-tab-span" style="white-space: pre;">    </span>Cоглашение&nbsp;— текст настоящего Соглашения между Администрацией и Участником, содержащий все необходимые и существенные условия о предоставлении прав использования Игры. Приложением к настоящему Соглашению и его неотъемлемой частью являются Условия участия, а также иные документы, ссылка на которые содержится в настоящем Соглашении.</div>

                                <div><br></div>

                                <div>1.8.<span class="Apple-tab-span" style="white-space: pre;">    </span>Условия участия&nbsp;(Условия) — Приложения к Соглашению, регламентирующие условия участия и поведения Участника в Игре, ограничения в действиях Участника в Игре, а также ответственность Участника за неисполнение таких Условий и несоблюдения ограничений, права Администрации на применение к Участнику определенных настоящим Соглашением мер и условия применения таких мер. Условия участия могут быть изменены Администрацией в любое время без предварительного уведомления Участника. О таких изменениях Администрация уведомляет Участника путем размещения информации на Сайте. Продолжение Участником участия в Игре после изменения Условия игры признается его согласием с такими изменениями.</div>

                                <div><br></div>

                                <div>2.<span class="Apple-tab-span" style="white-space: pre;">  </span>Условия присоединения к настоящему Соглашению</div>

                                <div>Перед тем как принять участие в Игре, Участник обязан ознакомиться с настоящим Соглашением, а также со всеми применимыми к Игре Условиями и иными документами, которые размещены в свободном доступе по адресу: http://lotzon.com.</div>

                                <div>После заполнения обязательных полей и ознакомления с Соглашением, Участник присоединяется (принимает) настоящее Соглашение, путем нажатия кнопки «Я ознакомился и согласен с правилами участия» или аналогичной, что является принятием (акцептом) оферты Администрации, а равно заключением договора, порождающего у Участника обязанности соблюдать условия Соглашения, в том числе применимых к Игре Условий. Фактическое использование Игры, также является акцептом настоящего Соглашения.</div>

                                <div>Участник дает согласие на обработку, сбор, накопление, хранение и использование информации о своих персональных данных.</div>

                                <div>Участник соглашается, понимает и принимает то обстоятельство, что Игра не является азартной игрой, конкурсом, лотереей, пари.</div>

                                <div><br></div>

                                <div>3.<span class="Apple-tab-span" style="white-space: pre;">  </span>Предмет Соглашения</div>

                                <div>По настоящему Соглашению и при условии соблюдения Участником его условий, Администрация предоставляет Участнику право использования Игры, в том числе доступ к участию в Игре, в пределах, определенных настоящим Соглашением.</div>

                                <div><br></div>

                                <div>4.<span class="Apple-tab-span" style="white-space: pre;">  </span>Пределы использования Игры</div>

                                <div>4.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Участник вправе использовать Игру следующими способами:</div>

                                <div>4.1.1.&nbsp;Участвовать в Игре путем регистрации и создания лишь одной учетной записи с соблюдением условий Соглашения.</div>

                                <div>4.1.2.&nbsp;Использовать Игру в рамках функционала, предоставляемого Администрацией.</div>

                                <div>4.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Участник может вывести денежные средства с Денежного счета в любую поддерживаемую Системой систему электронной оплаты на выбор.&nbsp;</div>

                                <div>4.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Все комиссии, связанные с выводом средств с Денежного счета Участника, оплачиваются Администрацией.</div>

                                <div>4.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Обработка заявок на вывод средств длится до 10-ти дней.</div>

                                <div>4.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Для получения средств Участник должен заполнить свои личные данные в личном кабинете.</div>

                                <div>4.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>При выводе денежных средств Участником, Администрация может запросить копии документов, удостоверяющих личность Участника.</div>

                                <div>4.7.<span class="Apple-tab-span" style="white-space: pre;">    </span>В случае возникновения технических сложностей с выводом денег с Денежного счета Участника, Администрация оставляет за собой право попросить Участника произвести вывод средств при помощи другой системы электронной оплаты.</div>

                                <div><br></div>

                                <div>5.<span class="Apple-tab-span" style="white-space: pre;">  </span>Участник не вправе:</div>

                                <div>5.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Использовать Игру иными способами, не предусмотренными настоящим Соглашением и выходящими за рамки обычного игрового процесса.</div>

                                <div>5.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Предоставлять Администрации вымышленную, неправдивую информацию о себе.</div>

                                <div>5.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Передавать предоставленные Участнику права использования Игры другим Участникам или третьим лицам любым способом.</div>

                                <div>5.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Распространять в коммерческих или некоммерческих целях информацию, полученную в рамках участия в Игре, вне рамок игрового процесса, без согласия Администрации.</div>

                                <div><br></div>

                                <div>6.<span class="Apple-tab-span" style="white-space: pre;">  </span>Обязанности Администрация</div>

                                <div>Администрация принимает на себя следующие обязательства:</div>

                                <div>6.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Гарантирует, что участие в Игре есть и всегда будет бесплатным. Основным принципом Игры является безоплатность.</div>

                                <div>6.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>На условиях, изложенных в настоящем Соглашении, предоставить возможность Участнику участвовать в Игре, осуществлять предоставление прав использования Игры.</div>

                                <div>6.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>С учетом условий, изложенных в настоящем Соглашении, обеспечить игровой процесс и сохранность информации о персональных данных Участника.</div>

                                <div>6.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Уведомлять Участника путем опубликования информации на Сайте об изменениях условий настоящего Соглашения.</div>

                                <div><br></div>

                                <div>7.<span class="Apple-tab-span" style="white-space: pre;">  </span>Права Администрации</div>

                                <div>Администрация имеет следующие права:</div>

                                <div>7.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>В любое время, в одностороннем порядке ограничить, расширить, изменить содержание и функционал Игры без предварительного уведомления Участника.</div>

                                <div>7.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>В любое время изменять, дополнять, модифицировать Игру, любую из ее частей, без какого-либо предварительного уведомления Участника.</div>

                                <div>7.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>В любое время изменить, удалить любую информацию, размещенную Участником на Ресурсе Администрации, включая высказывания, объявления Участника.</div>

                                <div>7.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>В любое время приостановить, ограничить и/или прекратить доступ Участника к Игре на условиях настоящего Соглашения, в том числе при несоблюдении Участником условий настоящего Соглашения или Условий участия.</div>

                                <div>7.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>В целях сбора статистических данных и идентификации Участника запрашивать, сохранять, хранить и использовать информацию об IP-адресах Участника, а также информацию о персональных данных Участника.</div>

                                <div>7.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>Использовать файлы технической информации (cookies), размещаемые на персональном компьютере Участника.</div>

                                <div>7.7.<span class="Apple-tab-span" style="white-space: pre;">    </span>Рассылать Участникам сообщения информационного или технического характера, связанного с Игрой.</div>

                                <div>7.8.<span class="Apple-tab-span" style="white-space: pre;">    </span>Во время игрового процесса делать замечания Участникам, предупреждать, уведомлять, информировать их о несоблюдении Условия игры, либо иных условий настоящего Соглашения. Указания Администрации, данные во время игрового процесса, обязательны для исполнения Участником.</div>

                                <div>7.9.<span class="Apple-tab-span" style="white-space: pre;">    </span>Предпринимать меры для защиты собственных интеллектуальных прав в отношении Игры.</div>

                                <div>7.10.&nbsp;В случае приостановления, ограничения, прекращения предоставления Участнику доступа к Игре в связи с нарушением Участником настоящего Соглашения или Условий участия, возобновить предоставление Участнику доступа к Игре на условиях досрочной разблокировки игрового аккаунта Участника. Порядок и условия такой разблокировки определяются на усмотрение Администрации.</div>

                                <div>7.11.&nbsp;В любое время прекратить предоставление доступа к Игре или возможность использовать Игру (закрыть Игру) и/или любой ее функционал без предварительного уведомления Участника.</div>

                                <div><br></div>

                                <div>8.<span class="Apple-tab-span" style="white-space: pre;">  </span>Ограничение ответственности Администрация</div>

                                <div>8.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за возможные противоправные действия Участника или третьих лиц.</div>

                                <div>8.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за высказывания Участника, опубликованные на Ресурсах Администрации. Администрация не несет ответственности за поведение Участника на Ресурсах Администрации.</div>

                                <div>8.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за утерю Участником возможности доступа к своему игровому аккаунту — учетной записи Участника в Игре (утрату логина, пароля, иной информации, необходимой для участия Участника в Игре).</div>

                                <div>8.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за неполное, неточное, некорректное указание Участником своих данных при создании учетной записи.</div>

                                <div>8.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за утрату Участником в ходе игрового процесса игровых ценностей, полученных в результате участия в Игре, при не соблюдении п. 9 п.п. 9.1.5.</div>

                                <div>8.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за отсутствие у Участника доступа в Интернет, за качество услуг провайдеров связи сети Интернет, с которыми Участник заключил соглашение о предоставлении услуг по доступу к сети Интернет.</div>

                                <div><br></div>

                                <div>8.7.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не гарантирует, что:</div>

                                <div>8.7.1.&nbsp;Игра будет удовлетворять субъективные требования и ожидания Участника.</div>

                                <div>8.7.2.&nbsp;Игровой процесс на Ресурсах Администрации, а также передача прав использования Игры будут протекать непрерывно, быстро, без технических сбоев, надежно и без ошибок.</div>

                                <div>8.7.3.&nbsp;Результаты, которые могут быть получены с использованием программного обеспечения и базы данных Игры, при участии в Игре будут безошибочными.</div>

                                <div>8.7.4.&nbsp;Качество игрового процесса, каких-либо аспектов Игры, информации, полученной в ходе Игры или базы данных, предоставляемых на Ресурсах Администрации, будет соответствовать ожиданиям Участника.</div>

                                <div>8.7.5.&nbsp;Игра будет доступна и может использоваться круглосуточно, в какой-то конкретный момент времени или в течение какого-либо периода времени.</div>

                                <div><br></div>

                                <div>8.8.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не несет ответственности за возникновение прямого или косвенного ущерба Участника либо иных третьих лиц, причиненного в результате:</div>

                                <div>8.8.1.&nbsp;Использования либо невозможности использования Ресурсов Администрации;</div>

                                <div>8.8.2.&nbsp;Несанкционированного доступа любых третьих лиц к личной информации Участника, включая учетную запись Участника, денежного счета Участника в Игре;</div>

                                <div>8.8.3.&nbsp;Заявления или поведения любого третьего лица на Ресурсах Администрации.</div>

                                <div><br></div>

                                <div>8.9.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация не обязана предоставлять Участнику какие-либо доказательства, документы и прочее, свидетельствующие о нарушении Участником условий Соглашения, в результате которого Участнику было отказано в предоставлении доступа к Игре, игровым ценностям, либо такой доступ был прекращен и/или ограничен.</div>

                                <div><br></div>

                                <div>9.<span class="Apple-tab-span" style="white-space: pre;">  </span>Обязанности Участника</div>

                                <div>9.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Участник обязан:</div>

                                <div>9.1.1.&nbsp;Соблюдать условия настоящего Соглашения, включая Условия участия, без каких-либо ограничений.</div>

                                <div>9.1.2.&nbsp;В момент регистрации на Ресурсе Администрации указывать достоверную информацию о себе и своих личных данных, в том числе об адресе электронной почты. Запрещено регистрироваться на Ресурсе с помощью временной электронной почты.</div>

                                <div>9.1.3.&nbsp;Не превышать пределов использования Игры, установленных в разделе 4 настоящего Соглашения.</div>

                                <div>9.1.4.&nbsp;Не нарушать права интеллектуальной собственности Администрации в отношении Игры и/или каких-либо составляющих Ресурсов Администрации, в частности, Участник не имеет права копировать, транслировать, рассылать, публиковать, и иным образом распространять и воспроизводить материалы (текстовые, графические, аудио-видео), находящиеся в составе Игровых ресурсов без письменного согласия Администрации.</div>

                                <div>9.1.5.&nbsp;Самостоятельно предпринимать должные меры, обеспечивающие безопасность его учетных записей в Игре и предотвращающие несанкционированное пользование третьими лицами этими учетными записями.</div>

                                <div>9.1.6.&nbsp;Выполнять указания Администрации, в частности, данные Администрацией Участнику в Игре, в центре поддержке пользователей (Участников), в новостном разделе Сайта, на форуме Администрации. В случае невыполнения Участником таких указаний Администрация имеет право приостановить, ограничить, прекратить предоставление Участнику доступа к Игре.</div>

                                <div>9.1.7.&nbsp;Отключить все системы блокировки показа рекламных сообщений (AdBlock и подобные).</div>

                                <div>9.1.8.&nbsp;Соблюдать иные требования и выполнять иные обязательства, предусмотренные настоящим Соглашением и Условиями участия.</div>

                                <div><br></div>

                                <div>9.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Участник гарантирует, что обладает всеми необходимыми полномочиями для заключения настоящего Соглашения.</div>

                                <div>9.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Иные обязательства Участника предусмотрены в Условиях Игры, а также в разделе 4.9. настоящего Соглашения.</div>

                                <div><br></div>

                                <div>10.<span class="Apple-tab-span" style="white-space: pre;"> </span>Территория и срок действия Соглашения</div>

                                <div style="">10.1.&nbsp;Участник вправе использовать Игру способами, описанными в настоящем Соглашении, на всей территории стран СНГ, а также иных территориях, на которых она доступна в рамках обычного игрового процесса с использованием стандартных компьютерных средств и программ в рамках функционала Ресурсов Администрации.</div>

                                <div>10.2.&nbsp;Настоящее Соглашение действует с момента принятия его условий Участником и действует бессрочно.</div>

                                <div>10.3.&nbsp;Действие настоящего Соглашения продолжается, если:</div>

                                <div>10.3.1.&nbsp;Администрация не примет решение об изменении положений настоящего Соглашения, о необходимости заключения с Участниками нового соглашения, прекращении обслуживания Игры и прекращении к ней доступа, прекращении настоящего Соглашения в отношении Участника, или прекращения доступа к Игре в отношении Участника.</div>

                                <div>10.3.2.&nbsp;Участник не примет решение о прекращении использования Игры.</div>

                                <div>10.4.&nbsp;Администрация вправе прекратить настоящее Соглашение в одностороннем внесудебном порядке с немедленным прекращением доступа и возможности использовать Игру, в случае:</div>

                                <div>10.4.1.&nbsp;Закрытия Игры.</div>

                                <div>10.4.2.&nbsp;Любого, в том числе однократного, нарушения Участником условий настоящего Соглашения или Условий участия.</div>

                                <div>10.5.&nbsp;Администрация вправе в любое время без уведомления Участника и без объяснения причин приостановить доступ и возможность использовать Игру без возмещения каких-либо затрат, убытков или возврата, полученного по Соглашению, в том числе в случае любого, в том числе однократного, нарушения Участником условий настоящего Соглашения или Условий игры.</div>

                                <div>10.6.&nbsp;Участник вправе в любое время без уведомления Администрации и без объяснения причин прекратить настоящее Соглашение в одностороннем внесудебном порядке путем удаления игрового аккаунта.</div>

                                <div>10.7.&nbsp;Участник соглашается и полностью признает, что все исключительные права на локализованную (переведенную на соответствующий язык) Игру, игровые предметы и аксессуары, внутриигровые ценности, графические изображения, фотографии, анимации, видеоизображения, видеоклипы, звуковые записи, звуковые эффекты, музыку, текстовое наполнение Игры и иные составляющие Игры, принадлежат Администрацияу, если иное в явном виде не указано в Соглашении, на Интернет сайте Игры или в самой Игре.</div>

                                <div>10.8.&nbsp;Участник не вправе использовать любые составляющие вне Игры и игрового процесса без письменного согласия Администрация.</div>

                                <div>10.9.&nbsp;Участник понимает, принимает и соглашается, что любой элемент Игры, в частности, являются составляющей частью Игры как программы и охраняются авторским правом.</div>

                                <div>10.10.&nbsp;Настоящее Соглашение не предусматривает уступку каких-либо исключительных прав или выдачу исключительной лицензии на любые составляющие Игры и/или Игровые Ресурсы от Администрация к Участнику.</div>

                                <div>10.11.&nbsp;В случае, если Участнику в соответствии с законами его государства запрещено пользоваться компьютерными играми в режиме он-лайн или существуют иные законодательные ограничения, включая ограничения по возрасту допуска к такому программному обеспечению, Участник не вправе использовать Игру. В таком случае Участник самостоятельно несет ответственность за использование Игры на территории своего государства и нарушение местного законодательства.</div>

                                <div>10.12.&nbsp;Настоящее Соглашение может быть изменено Администрация без какого-либо предварительного уведомления. Любые изменения в Соглашении, осуществленные Администрация в одностороннем порядке вступают в силу в день, следующий за днем опубликования таких изменений на Интернет сайте Администрация. Участник обязуется самостоятельно проверять Соглашение на предмет изменений. Неосуществление Участником действий по ознакомлению с Соглашением и/или измененной редакцией Соглашения не может служить основанием для неисполнения Участником своих обязательств и несоблюдения Участником ограничений, установленных Соглашением.</div>

                                <div>10.13.&nbsp;Недействительность одного или нескольких положений Соглашения, признанная в установленном порядке вступившим в силу решением суда, не влечет для Сторон недействительности соглашения в целом. В случае признания одного или нескольких положений Соглашения в установленном порядке недействительными, Стороны обязуются исполнять взятые на себя по Соглашению обязательства максимально близким к подразумеваемым Сторонами при заключении и/или согласованном изменении Соглашения способом.</div>

                                <div>10.14.&nbsp;Настоящее Соглашение и взаимоотношения Сторон в связи с настоящим Соглашением и использованием Игры регулируются законодательством государства Участника.</div>

                                <div>10.15.&nbsp;В отношении формы и способа заключения настоящего Соглашения применяются нормы действующего законодательства, регулирующие порядок и условия заключения договора путем акцепта публичной оферты.</div>

                                <div>10.16.&nbsp;Все споры сторон по настоящему соглашению подлежат разрешению путем переписки и переговоров с использованием обязательного досудебного (претензионного) порядка. В случае невозможности достичь согласия между сторонами путем переговоров в течение 60 (шестидесяти) календарных дней с момента получения другой Стороной письменной претензии, рассмотрение спора должно быть передано любой заинтересованной стороной в суд общей юрисдикции по месту нахождения Администрация (с исключением подсудности дела любым иным судам).</div>

                                <div>10.17.&nbsp;По вопросам, связанным с исполнением Соглашения, просьба обращаться по адресу местонахождения Администрация.</div>

                                <div><br></div>

                                <div>Редакция Соглашения от 14.10.2014 г.</div>

                                <div><br></div>

                                <div>Условия участия</div>

                                <div><span class="Apple-tab-span" style="white-space: pre;">        </span></div>

                                <div>1.<span class="Apple-tab-span" style="white-space: pre;">  </span>Взаимодействие с Администрацией</div>

                                <div>1.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено неуважительное отношение к Администрации.</div>

                                <div>1.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещены угрозы любого характера в адрес Администрации.</div>

                                <div>1.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещен обман Администрации.</div>

                                <div>1.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещена публикация бесед с Администрацией, либо их содержания без предварительного согласования.</div>

                                <div>1.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено распространять слухи, клевету о Игре и Администрации.</div>

                                <div><br></div>

                                <div>2.<span class="Apple-tab-span" style="white-space: pre;">  </span>Обязанности модератора</div>

                                <div>2.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератор имеет право вынести предупреждение Участнику, нарушившему Условия, если нарушение было незначительным.</div>

                                <div>2.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератор имеет право без предупреждения блокировать отправку сообщений чата Участнику, нарушившему Условия.</div>

                                <div>2.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>При повторном нарушении правил Модератор имеет право увеличить срок наказания.</div>

                                <div>2.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератор обязан быть честным и беспристрастным в отношении всех Участников.</div>

                                <div>2.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератор обязан поддерживать порядок в чате.</div>

                                <div>2.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератор обязан фиксировать все вынесенные им наказания.</div>

                                <div>2.7.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератору запрещается злоупотреблять своим служебным положением, ииспользовать его для достижения какой-либо выгоды.</div>

                                <div><br></div>

                                <div>3.<span class="Apple-tab-span" style="white-space: pre;">  </span>Игровой процесс</div>

                                <div>3.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено использование программ эмулирующих присутствие игрока в игре или нарушающих нормальное функционирование серверного ПО.&nbsp;</div>

                                <div>3.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещен несанкционированный доступ к чужой игровой учетной записи.</div>

                                <div>3.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещены угрозы в любом проявлении в сторону Участников.</div>

                                <div>3.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено отправлять в общий чат сообщения, нарушающие законы.</div>

                                <div>3.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено явно или косвенно рекламировать другие онлайновые игры, любые другие посторонние проекты.</div>

                                <div>3.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено публиковать содержание личных бесед с кем-либо из Администрации без предварительного согласия.</div>

                                <div>3.7.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено явно или косвенно рекламировать сексуальные услуги и/или материалы эротического (порнографического) характера.</div>

                                <div>3.8.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещены любые проявления расизма и национализма.</div>

                                <div><br></div>

                                <div>4.<span class="Apple-tab-span" style="white-space: pre;">  </span>Использование ошибок сервера (багов)</div>

                                <div>4.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено использование ошибок сервера/сайта (багов).</div>

                                <div>4.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Если вы нашли ошибку сервера/сайта (баг), вы обязаны сообщить об этом любому представителю Администрации.</div>

                                <div><br></div>

                                <div>5.<span class="Apple-tab-span" style="white-space: pre;">  </span>Общение, присвоение имен</div>

                                <div>5.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Категорически запрещено использование нецензурных выражений и оскорбление Участников как в Игре, так и в чате.</div>

                                <div>5.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено выдавать себя за Администрацию или за доверенное лицо Администрации.</div>

                                <div>5.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещены флуд, мешающий общению других игроков, спам и целенаправленные действия по засорению игрового чата. Однотипные сообщения, а также сообщения о продаже, покупке и обмене.</div>

                                <div>5.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Запрещено злоупотребление верхним регистром, а также использование «прыгающего текста».</div>

                                <div><br></div>

                                <div>6.<span class="Apple-tab-span" style="white-space: pre;">  </span>Дополнение правил</div>

                                <div>6.1.<span class="Apple-tab-span" style="white-space: pre;">    </span>Данные Условия могут быть дополнены без предупреждения. Участник обязуется следить за обновлениями Условий.</div>

                                <div>6.2.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация имеет право выбирать меру наказания, исходя из ситуации нарушения данных Условий. Как в сторону смягчения, так и в сторону увеличения наказания.</div>

                                <div>6.3.<span class="Apple-tab-span" style="white-space: pre;">    </span>Модератор является представителем Администрации.</div>

                                <div>6.4.<span class="Apple-tab-span" style="white-space: pre;">    </span>Пользователь обязан самостоятельно обеспечивать сложность и секретность своего пароля и иных необходимых данных. Пользователь также несет ответственность за неразглашение своего логина и пароля, а также все риски (убытки), связанные с возможным разглашением.</div>

                                <div>6.5.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация имеет право без предупреждения ограничивать Участнику функционал Игры (общение в игровом чате и/или доступ к учетной записи Участника) за нарушение данных Условий и/или Соглашения, а также за действия, явно наносящие ущерб организации Игры и/или игровому процессу, но прямо не оговоренных в данных Условиях.</div>

                                <div>6.6.<span class="Apple-tab-span" style="white-space: pre;">    </span>Администрация имеет право удалить или заблокировать учетную запись Участника при неоднократном нарушении данных Условий. При этом Администрация не возмещает и не компенсирует Участнику полученные материальные средства.</div>
            </div>
        </div>
    </div>
    <!-- INFO POPUP -->
	<script>
    
    // $('.pi-inp-bk input').on('focus', function(){
    //     $(this).closest('.pi-inp-bk').addClass('focus')
    //     if($(this).attr('name') == 'date')$(this).attr('type','date');
    //     $('.profile-info .save-bk .sb-ch-td .but').addClass('save');
    // });

    // $('.pi-inp-bk input').on('blur', function(){
    //     $(this).closest('.pi-inp-bk').removeClass('focus')
    //     if($(this).attr('name') == 'date')$(this).attr('type','text');
    // });

    // $('#mail-conf .pi-inp-bk input').on('keyup', function(){
    //     var val = $.trim($(this).val().length);
    //     if(val > 0){
    //         $(this).closest('.ml-cn-padd').find('.ml-cn-but').removeClass('disabled');
    //     }else{
    //         $(this).closest('.ml-cn-padd').find('.ml-cn-but').addClass('disabled');
    //     }
    // });
    
    // registration handler
    // $('#mail-conf .ml-cn-but').on('click', function(e) {
    //     var form = $('#login-block form[name="register"]');
    //     var email = $('#mail-conf').find('input[name="addr"]').val();
    //     var rulesAgree = 1;
    //     var ref = form.data('ref');
    //     registerPlayer({'email':email, 'agree':rulesAgree, 'ref':ref}, function(data){
    //         // success
    //     }, function(data){
    //         $('#mail-conf .alert').text(data.message);
    //     }, function(data) {});
    //     return false;
    // });
    
    </script>	

    <script src="/res/js/landing/promo.js"></script>
    <script src="/res/js/landing/backend.js"></script>

	<?php if(!$metrika['metrikaDisabled']):?>

		<?php if($metrika['googleAnalytics']): ?>
    		<script>
    			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    			ga('create', '<?php echo $metrika['googleAnalytics'];?>', 'auto');
    			ga('send', 'pageview');

    		</script>
    	<?php endif; ?>

    	<?php if($metrika['yandexMetrika']): ?>
    		<!-- Yandex.Metrika counter -->
    		<script type="text/javascript">
    			(function (d, w, c) {
    				(w[c] = w[c] || []).push(function() {
    					try {
    						w.yaCounter<?php echo $metrika['yandexMetrika'];?> = new Ya.Metrika({
    							id:<?php echo $metrika['yandexMetrika'];?>,
    							clickmap:true,
    							trackLinks:true,
    							accurateTrackBounce:true,
    							webvisor:true,
    							trackHash: true
    						});
    					} catch(e) { }
    				});

    				var n = d.getElementsByTagName("script")[0],
    					s = d.createElement("script"),
    					f = function () { n.parentNode.insertBefore(s, n); };
    				s.type = "text/javascript";
    				s.async = true;
    				s.src = "https://mc.yandex.ru/metrika/watch.js";

    				if (w.opera == "[object Opera]") {
    					d.addEventListener("DOMContentLoaded", f, false);
    				} else { f(); }
    			})(document, window, "yandex_metrika_callbacks");
    		</script>
    		<noscript><div><img src="https://mc.yandex.ru/watch/<?php echo $metrika['yandexMetrika'];?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    		<!-- /Yandex.Metrika counter -->
    	<?php endif; ?>
	<?php endif; ?>

    <?php
    switch ($error['code']) {
        case 428:
            // при регистрации соц.сеть не выдала email
            // $error['message'] - сообщение NEED_EMAIL
            // var_dump($error);
            ?>
                <script>
                    // alert(428);
                    $('.popup').addClass('social');
                    landing.popup.open();
                </script>                            
            <?php
            break;
        case 409:
            // попытка зарегистрироваться через соц.сеть, на которую уже зарегистирован аккаунт
            // $error['message'] - сообщение USER_ALREADY_EXISTS
            // $socialName   - имя социальной сети ("Vkontakte", "Facebook", "Odnoklassniki")
            ?>
                <script>
                    // alert(409);
                    var name = "<?php echo $socialName; ?>";
                    if(name){
                        $('.popup').addClass('social_exist');
                        landing.popup.open();

                        $('form[name="social_exist"]').submit(function(event){
                            window.location.href = "./auth/"+name+"?method=log-in";
                            return false;
                        });
                    }
                </script>                
            <?php
            break;
        case 423:
            // выполнялся вход через соц.сеть, но пользователя для такой соц.сети не существует
            // доступ заблокирован
            // $error['message'] - сообщение ACCESS_DENIED
            ?>
                <script>
                    // alert(423);
                    $('.popup').addClass('social_error');
                    landing.popup.open();
                </script>                
            <?php
            break;
        case 404:
            // выполнялся вход через соц.сеть, но пользователя для такой соц.сети не существует
            // $error['message'] - сообщение USER_NOT_FOUND
            // либо доступ заблокирован
            // $error['message'] - сообщение ACCESS_DENIED
            ?>
                <script>
                    // alert(404+", "+423 );
                    
                    var error = '<?php echo $error['message']; ?>';
                    if (error){
                        document.querySelector('#login-block-form .alert').innerHTML = error;
                        landing.formError( $('#login-block-form') );
                    }
                </script>                
            <?php
            break;
        case 400:
        case 500:
            // ошибки в запросе, либо на сервере
            // $error['message'] - сообщение BAD_REQUEST
            // $error['message'] - сообщение INTERNAL_SERVER_ERROR
            ?>
                <script>
                    // alert(400+','+500);
                    $('.popup').addClass('unexpected_error');
                    landing.popup.open();
                </script>                
            <?php
            break;
        default:
            if ($socialIdentity) {
                // регистрация через соц.сеть ок! нужен теперь email или подтверждение того, что вернула соц.сеть
                // $socialIdentity->getSocialEmail() - email который выдала соц.сеть
                ?>
                <script>
                    var curmail = "<?php echo $socialIdentity->getSocialEmail();?>";
                    if(curmail){
                        // alert('$socialIdentity '+curmail);
                        $('.popup form[name="social_register"] .current-mail').html(curmail);
                        $('.popup form[name="social_register"] input[type="email"]').attr('data-current', curmail);

                        $('.popup').addClass('social_register');
                        landing.popup.open();
                    }
                </script>                
                <?php
            }
    }
    ?>

</body>
</html>


